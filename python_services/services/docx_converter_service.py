"""
DOCX to PDF Converter Service

Handles conversion of DOC/DOCX files to PDF using LibreOffice
Falls back to docx2pdf if LibreOffice is not available
"""

import io
import os
import base64
import subprocess
import tempfile
import shutil
from typing import Dict, Any
from pathlib import Path

try:
    import docx2pdf
    DOCX2PDF_AVAILABLE = True
except ImportError:
    DOCX2PDF_AVAILABLE = False

from utils.logger import setup_logger

logger = setup_logger(__name__, 'docx_converter.log')


class DocxConverterService:
    """Service for converting DOCX/DOC files to PDF."""
    
    def __init__(self):
        self.max_file_size = 50 * 1024 * 1024  # 50MB limit
        self.libreoffice_path = self._find_libreoffice()
        logger.info(f"DOCX Converter initialized - LibreOffice: {self.libreoffice_path or 'Not found'}")
    
    def _find_libreoffice(self) -> str:
        """Find LibreOffice installation."""
        possible_paths = [
            # Windows
            r"C:\Program Files\LibreOffice\program\soffice.exe",
            r"C:\Program Files (x86)\LibreOffice\program\soffice.exe",
            # Linux
            "/usr/bin/libreoffice",
            "/usr/bin/soffice",
            # Mac
            "/Applications/LibreOffice.app/Contents/MacOS/soffice",
        ]
        
        for path in possible_paths:
            if os.path.exists(path):
                return path
        
        # Try to find in PATH
        try:
            result = subprocess.run(
                ['which', 'libreoffice'],
                capture_output=True,
                text=True,
                timeout=5
            )
            if result.returncode == 0:
                return result.stdout.strip()
        except:
            pass
        
        try:
            result = subprocess.run(
                ['which', 'soffice'],
                capture_output=True,
                text=True,
                timeout=5
            )
            if result.returncode == 0:
                return result.stdout.strip()
        except:
            pass
        
        return None
    
    def is_libreoffice_available(self) -> bool:
        """Check if LibreOffice is available."""
        if not self.libreoffice_path:
            return False
        
        try:
            result = subprocess.run(
                [self.libreoffice_path, '--version'],
                capture_output=True,
                text=True,
                timeout=10
            )
            return result.returncode == 0
        except Exception as e:
            logger.warning(f"LibreOffice check failed: {str(e)}")
            return False
    
    def convert_to_pdf(self, file_content: bytes, filename: str) -> Dict[str, Any]:
        """
        Convert DOCX/DOC file to PDF.
        
        Args:
            file_content: File content as bytes
            filename: Original filename
        
        Returns:
            Dict with success status and PDF data
        """
        temp_dir = None
        try:
            # Validate file size
            if len(file_content) > self.max_file_size:
                return {
                    'success': False,
                    'error': f'File too large: {len(file_content)} bytes (max: {self.max_file_size})'
                }
            
            logger.info(f"Converting {filename} to PDF")
            
            # Create temporary directory
            temp_dir = tempfile.mkdtemp(prefix='docx_convert_')
            input_path = os.path.join(temp_dir, filename)
            
            # Write input file
            with open(input_path, 'wb') as f:
                f.write(file_content)
            
            # Try LibreOffice first
            if self.libreoffice_path:
                try:
                    pdf_data = self._convert_with_libreoffice(input_path, temp_dir)
                    if pdf_data:
                        logger.info(f"Successfully converted {filename} using LibreOffice")
                        return {
                            'success': True,
                            'pdf_data': base64.b64encode(pdf_data).decode('utf-8'),
                            'filename': filename.rsplit('.', 1)[0] + '.pdf',
                            'method': 'libreoffice',
                            'message': 'Conversion successful'
                        }
                except Exception as e:
                    logger.warning(f"LibreOffice conversion failed: {str(e)}")
            
            # Fall back to docx2pdf
            if DOCX2PDF_AVAILABLE:
                try:
                    pdf_data = self._convert_with_docx2pdf(input_path, temp_dir)
                    if pdf_data:
                        logger.info(f"Successfully converted {filename} using docx2pdf")
                        return {
                            'success': True,
                            'pdf_data': base64.b64encode(pdf_data).decode('utf-8'),
                            'filename': filename.rsplit('.', 1)[0] + '.pdf',
                            'method': 'docx2pdf',
                            'message': 'Conversion successful'
                        }
                except Exception as e:
                    logger.warning(f"docx2pdf conversion failed: {str(e)}")
            
            return {
                'success': False,
                'error': 'No conversion method available. Please install LibreOffice.'
            }
        
        except Exception as e:
            logger.error(f"Conversion failed: {str(e)}")
            return {
                'success': False,
                'error': str(e)
            }
        
        finally:
            # Clean up temporary directory
            if temp_dir and os.path.exists(temp_dir):
                try:
                    shutil.rmtree(temp_dir)
                except Exception as e:
                    logger.warning(f"Failed to clean up temp directory: {str(e)}")
    
    def _convert_with_libreoffice(self, input_path: str, output_dir: str) -> bytes:
        """Convert using LibreOffice."""
        try:
            # Run LibreOffice conversion
            cmd = [
                self.libreoffice_path,
                '--headless',
                '--convert-to',
                'pdf',
                '--outdir',
                output_dir,
                input_path
            ]
            
            result = subprocess.run(
                cmd,
                capture_output=True,
                text=True,
                timeout=120
            )
            
            if result.returncode != 0:
                raise Exception(f"LibreOffice conversion failed: {result.stderr}")
            
            # Find the output PDF
            input_name = os.path.basename(input_path)
            pdf_name = input_name.rsplit('.', 1)[0] + '.pdf'
            pdf_path = os.path.join(output_dir, pdf_name)
            
            if not os.path.exists(pdf_path):
                raise Exception("PDF output file not found")
            
            # Read PDF data
            with open(pdf_path, 'rb') as f:
                return f.read()
        
        except Exception as e:
            logger.error(f"LibreOffice conversion error: {str(e)}")
            raise
    
    def _convert_with_docx2pdf(self, input_path: str, output_dir: str) -> bytes:
        """Convert using docx2pdf library."""
        try:
            output_path = os.path.join(
                output_dir,
                os.path.basename(input_path).rsplit('.', 1)[0] + '.pdf'
            )
            
            docx2pdf.convert(input_path, output_path)
            
            if not os.path.exists(output_path):
                raise Exception("PDF output file not found")
            
            with open(output_path, 'rb') as f:
                return f.read()
        
        except Exception as e:
            logger.error(f"docx2pdf conversion error: {str(e)}")
            raise

