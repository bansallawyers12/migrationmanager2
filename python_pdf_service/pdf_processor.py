#!/usr/bin/env python3
"""
PDF Processing Microservice for E-Signature App
Replaces Ghostscript and PHP PDF libraries with Python-based solutions
"""

import os
import sys
import json
import base64
import logging
from pathlib import Path
from typing import Dict, List, Optional
from dataclasses import dataclass
from datetime import datetime

try:
    import fitz  # PyMuPDF
    from PIL import Image
    from flask import Flask, request, jsonify
    from flask_cors import CORS
except ImportError as e:
    print(f"Missing required package: {e}")
    print("Install with: pip install PyMuPDF Pillow Flask flask-cors")
    sys.exit(1)

# Configure logging
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s'
)
logger = logging.getLogger(__name__)

app = Flask(__name__)
CORS(app)  # Enable CORS for all routes

@dataclass
class SignatureInfo:
    """Signature field information"""
    field_id: int
    page_number: int
    x_percent: float
    y_percent: float
    width_percent: float
    height_percent: float
    signature_data: str  # Base64 encoded PNG

class PDFProcessor:
    """Handles PDF operations using PyMuPDF"""
    
    def __init__(self):
        self.supported_formats = ['.pdf']
        self.max_file_size = 50 * 1024 * 1024  # 50MB limit
        
    def validate_pdf(self, file_path: str) -> bool:
        """Validate PDF file"""
        try:
            if not os.path.exists(file_path):
                logger.error(f"File not found: {file_path}")
                return False
                
            file_size = os.path.getsize(file_path)
            if file_size > self.max_file_size:
                logger.warning(f"File too large: {file_size} bytes")
                return False
                
            # Try to open with PyMuPDF
            with fitz.open(file_path) as doc:
                if doc.page_count == 0:
                    return False
                return True
                
        except Exception as e:
            logger.error(f"PDF validation failed: {e}")
            return False
    
    def get_pdf_info(self, file_path: str) -> Dict:
        """Extract PDF metadata and page information"""
        try:
            with fitz.open(file_path) as doc:
                info = {
                    'page_count': doc.page_count,
                    'metadata': doc.metadata,
                    'pages': []
                }
                
                for page_num in range(doc.page_count):
                    page = doc.load_page(page_num)
                    rect = page.rect
                    info['pages'].append({
                        'page_number': page_num + 1,
                        'width': rect.width,
                        'height': rect.height,
                        'rotation': page.rotation
                    })
                    
                return info
                
        except Exception as e:
            logger.error(f"Failed to get PDF info: {e}")
            return {}
    
    def convert_page_to_image(self, file_path: str, page_number: int, 
                            resolution: int = 150, format: str = 'PNG') -> Optional[bytes]:
        """Convert PDF page to image"""
        try:
            if not os.path.exists(file_path):
                logger.error(f"File not found: {file_path}")
                return None
                
            with fitz.open(file_path) as doc:
                if page_number > doc.page_count or page_number < 1:
                    logger.error(f"Page {page_number} exceeds document length ({doc.page_count})")
                    return None
                    
                page = doc.load_page(page_number - 1)  # PyMuPDF uses 0-based indexing
                
                # Calculate zoom factor for desired resolution
                zoom = resolution / 72  # 72 DPI is default
                mat = fitz.Matrix(zoom, zoom)
                
                # Render page to pixmap
                pix = page.get_pixmap(matrix=mat)
                
                # Convert to bytes
                img_data = pix.tobytes("png")
                
                logger.info(f"Successfully converted page {page_number} to image (resolution: {resolution})")
                return img_data
                
        except Exception as e:
            logger.error(f"Failed to convert page {page_number}: {e}")
            return None
    
    def normalize_pdf(self, input_path: str, output_path: str) -> bool:
        """Normalize PDF for better compatibility"""
        try:
            with fitz.open(input_path) as doc:
                # Create new document
                new_doc = fitz.open()
                
                for page_num in range(doc.page_count):
                    page = doc.load_page(page_num)
                    new_doc.insert_pdf(doc, from_page=page_num, to_page=page_num)
                
                # Save normalized PDF
                new_doc.save(output_path, garbage=4, deflate=True)
                new_doc.close()
                
                logger.info(f"PDF normalized successfully: {output_path}")
                return True
                
        except Exception as e:
            logger.error(f"PDF normalization failed: {e}")
            return False
    
    def add_signatures_to_pdf(self, input_path: str, output_path: str, 
                             signatures: List[SignatureInfo]) -> bool:
        """Add signatures to PDF at specified positions"""
        try:
            with fitz.open(input_path) as doc:
                # Group signatures by page
                signatures_by_page = {}
                for sig in signatures:
                    if sig.page_number not in signatures_by_page:
                        signatures_by_page[sig.page_number] = []
                    signatures_by_page[sig.page_number].append(sig)
                
                # Process each page
                for page_num in range(doc.page_count):
                    page = doc.load_page(page_num)
                    current_page = page_num + 1
                    
                    if current_page in signatures_by_page:
                        for sig in signatures_by_page[current_page]:
                            # Decode signature image
                            try:
                                # Handle data URI format
                                if ',' in sig.signature_data:
                                    signature_bytes = base64.b64decode(sig.signature_data.split(',')[1])
                                else:
                                    signature_bytes = base64.b64decode(sig.signature_data)
                                
                                # Create temporary signature file
                                temp_sig_path = f"temp_sig_{sig.field_id}_{os.getpid()}.png"
                                with open(temp_sig_path, 'wb') as f:
                                    f.write(signature_bytes)
                                
                                # Calculate signature position
                                rect = page.rect
                                x_pos = sig.x_percent * rect.width
                                y_pos = sig.y_percent * rect.height
                                width = sig.width_percent * rect.width
                                height = sig.height_percent * rect.height
                                
                                # Insert signature image
                                page.insert_image(
                                    fitz.Rect(x_pos, y_pos, x_pos + width, y_pos + height),
                                    filename=temp_sig_path
                                )
                                
                                # Clean up temp file
                                if os.path.exists(temp_sig_path):
                                    os.unlink(temp_sig_path)
                                
                            except Exception as e:
                                logger.error(f"Failed to add signature {sig.field_id}: {e}")
                                continue
                
                # Save signed PDF
                doc.save(output_path, garbage=4, deflate=True)
                logger.info(f"Signatures added successfully: {output_path}")
                return True
                
        except Exception as e:
            logger.error(f"Failed to add signatures: {e}")
            return False

# Initialize PDF processor
pdf_processor = PDFProcessor()

@app.route('/health', methods=['GET'])
def health_check():
    """Health check endpoint"""
    return jsonify({
        'status': 'healthy',
        'service': 'pdf-processor',
        'version': '1.0.0',
        'timestamp': datetime.now().isoformat()
    })

@app.route('/validate_pdf', methods=['POST'])
def validate_pdf():
    """Validate PDF file"""
    try:
        data = request.get_json()
        file_path = data.get('file_path')
        
        if not file_path:
            return jsonify({'error': 'file_path is required'}), 400
            
        is_valid = pdf_processor.validate_pdf(file_path)
        
        return jsonify({
            'valid': is_valid,
            'file_path': file_path
        })
        
    except Exception as e:
        logger.error(f"Validation error: {e}")
        return jsonify({'error': str(e)}), 500

@app.route('/pdf_info', methods=['POST'])
def get_pdf_info():
    """Get PDF information"""
    try:
        data = request.get_json()
        file_path = data.get('file_path')
        
        if not file_path:
            return jsonify({'error': 'file_path is required'}), 400
            
        info = pdf_processor.get_pdf_info(file_path)
        
        return jsonify(info)
        
    except Exception as e:
        logger.error(f"PDF info error: {e}")
        return jsonify({'error': str(e)}), 500

@app.route('/convert_page', methods=['POST'])
def convert_page():
    """Convert PDF page to image"""
    try:
        data = request.get_json()
        file_path = data.get('file_path')
        page_number = data.get('page_number', 1)
        resolution = data.get('resolution', 150)
        
        if not file_path:
            return jsonify({'error': 'file_path is required'}), 400
            
        image_data = pdf_processor.convert_page_to_image(
            file_path, page_number, resolution
        )
        
        if image_data:
            # Return base64 encoded image
            encoded_image = base64.b64encode(image_data).decode('utf-8')
            return jsonify({
                'success': True,
                'image_data': f"data:image/png;base64,{encoded_image}",
                'page_number': page_number,
                'resolution': resolution
            })
        else:
            return jsonify({'error': 'Failed to convert page'}), 500
            
    except Exception as e:
        logger.error(f"Page conversion error: {e}")
        return jsonify({'error': str(e)}), 500

@app.route('/normalize_pdf', methods=['POST'])
def normalize_pdf():
    """Normalize PDF file"""
    try:
        data = request.get_json()
        input_path = data.get('input_path')
        output_path = data.get('output_path')
        
        if not input_path or not output_path:
            return jsonify({'error': 'input_path and output_path are required'}), 400
            
        success = pdf_processor.normalize_pdf(input_path, output_path)
        
        return jsonify({
            'success': success,
            'input_path': input_path,
            'output_path': output_path
        })
        
    except Exception as e:
        logger.error(f"Normalization error: {e}")
        return jsonify({'error': str(e)}), 500

@app.route('/add_signatures', methods=['POST'])
def add_signatures():
    """Add signatures to PDF"""
    try:
        data = request.get_json()
        input_path = data.get('input_path')
        output_path = data.get('output_path')
        signatures_data = data.get('signatures', [])
        
        if not input_path or not output_path:
            return jsonify({'error': 'input_path and output_path are required'}), 400
            
        # Convert signatures data to SignatureInfo objects
        signatures = []
        for sig_data in signatures_data:
            sig = SignatureInfo(
                field_id=sig_data['field_id'],
                page_number=sig_data['page_number'],
                x_percent=sig_data['x_percent'],
                y_percent=sig_data['y_percent'],
                width_percent=sig_data['width_percent'],
                height_percent=sig_data['height_percent'],
                signature_data=sig_data['signature_data']
            )
            signatures.append(sig)
        
        success = pdf_processor.add_signatures_to_pdf(input_path, output_path, signatures)
        
        return jsonify({
            'success': success,
            'input_path': input_path,
            'output_path': output_path,
            'signatures_count': len(signatures)
        })
        
    except Exception as e:
        logger.error(f"Add signatures error: {e}")
        return jsonify({'error': str(e)}), 500

@app.route('/batch_convert', methods=['POST'])
def batch_convert():
    """Convert multiple PDF pages to images"""
    try:
        data = request.get_json()
        file_path = data.get('file_path')
        pages = data.get('pages', [])  # List of page numbers
        resolution = data.get('resolution', 150)
        
        if not file_path or not pages:
            return jsonify({'error': 'file_path and pages are required'}), 400
            
        results = {}
        for page_num in pages:
            image_data = pdf_processor.convert_page_to_image(file_path, page_num, resolution)
            if image_data:
                encoded_image = base64.b64encode(image_data).decode('utf-8')
                results[page_num] = f"data:image/png;base64,{encoded_image}"
            else:
                results[page_num] = None
        
        return jsonify({
            'success': True,
            'results': results,
            'total_pages': len(pages)
        })
        
    except Exception as e:
        logger.error(f"Batch conversion error: {e}")
        return jsonify({'error': str(e)}), 500

if __name__ == '__main__':
    logger.info("Starting PDF Processing Microservice...")
    logger.info("Available endpoints:")
    logger.info("  GET  /health - Health check")
    logger.info("  POST /validate_pdf - Validate PDF file")
    logger.info("  POST /pdf_info - Get PDF information")
    logger.info("  POST /convert_page - Convert page to image")
    logger.info("  POST /normalize_pdf - Normalize PDF")
    logger.info("  POST /add_signatures - Add signatures to PDF")
    logger.info("  POST /batch_convert - Convert multiple pages")
    
    app.run(host='127.0.0.1', port=5000, debug=False)

