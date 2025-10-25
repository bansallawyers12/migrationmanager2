from flask import Flask, request, jsonify, send_file
import os
import tempfile
import subprocess
import platform
import base64
import time
import shutil

app = Flask(__name__)

def find_libreoffice():
    """Find LibreOffice installation path"""
    if platform.system() == "Windows":
        # Common LibreOffice paths on Windows
        libreoffice_paths = [
            r"C:\Program Files\LibreOffice\program\soffice.exe",
            r"C:\Program Files (x86)\LibreOffice\program\soffice.exe",
            r"C:\Program Files\LibreOffice*\program\soffice.exe"
        ]
        
        for path in libreoffice_paths:
            if os.path.exists(path):
                return path
                
        # Try to find in PATH
        try:
            result = subprocess.run(['where', 'soffice'], capture_output=True, text=True)
            if result.returncode == 0:
                return result.stdout.strip().split('\n')[0]
        except:
            pass
            
    else:
        # Linux/Mac paths
        libreoffice_paths = [
            "libreoffice",
            "soffice", 
            "/usr/bin/libreoffice",
            "/usr/bin/soffice",
            "/Applications/LibreOffice.app/Contents/MacOS/soffice"
        ]
        
        for cmd in libreoffice_paths:
            try:
                result = subprocess.run([cmd, "--version"], capture_output=True, text=True, timeout=10)
                if result.returncode == 0:
                    return cmd
            except:
                continue
    
    return None

def convert_with_libreoffice(input_path, output_path):
    """Convert DOCX to PDF using LibreOffice"""
    libreoffice_cmd = find_libreoffice()
    
    if not libreoffice_cmd:
        raise Exception("LibreOffice not found. Please install LibreOffice first.")
    
    try:
        # Create output directory if it doesn't exist
        output_dir = os.path.dirname(output_path)
        if output_dir and not os.path.exists(output_dir):
            os.makedirs(output_dir)
        
        # LibreOffice command for conversion
        cmd = [
            libreoffice_cmd,
            "--headless",           # Run without GUI
            "--convert-to", "pdf",  # Convert to PDF
            "--outdir", output_dir, # Output directory
            input_path              # Input file
        ]
        
        print(f"Running command: {' '.join(cmd)}")
        
        # Run conversion with timeout
        result = subprocess.run(
            cmd, 
            capture_output=True, 
            text=True, 
            timeout=120  # 2 minutes timeout
        )
        
        if result.returncode != 0:
            raise Exception(f"LibreOffice conversion failed: {result.stderr}")
        
        # LibreOffice creates PDF with different name, find and rename it
        input_filename = os.path.basename(input_path)
        expected_pdf_name = input_filename.replace('.docx', '.pdf').replace('.doc', '.pdf')
        expected_pdf_path = os.path.join(output_dir, expected_pdf_name)
        
        if os.path.exists(expected_pdf_path):
            # Rename to our desired output path
            shutil.move(expected_pdf_path, output_path)
            return True
        else:
            # Look for any PDF file in the output directory
            for file in os.listdir(output_dir):
                if file.endswith('.pdf'):
                    pdf_path = os.path.join(output_dir, file)
                    shutil.move(pdf_path, output_path)
                    return True
            
            raise Exception("PDF file not found after conversion")
            
    except subprocess.TimeoutExpired:
        raise Exception("Conversion timed out (120 seconds)")
    except Exception as e:
        raise Exception(f"LibreOffice conversion error: {str(e)}")

def validate_file(file):
    """Validate uploaded file"""
    if not file:
        raise Exception("No file provided")
    
    if file.filename == '':
        raise Exception("No file selected")
    
    # Check file extension
    allowed_extensions = ['.doc', '.docx']
    file_ext = os.path.splitext(file.filename.lower())[1]
    
    if file_ext not in allowed_extensions:
        raise Exception(f"Invalid file format. Allowed formats: {', '.join(allowed_extensions)}")
    
    # Check file size (50MB limit)
    max_size = 50 * 1024 * 1024  # 50MB
    if len(file.read()) > max_size:
        file.seek(0)  # Reset file pointer
        raise Exception("File too large. Maximum size is 50MB.")
    
    file.seek(0)  # Reset file pointer
    return True

@app.route('/health', methods=['GET'])
def health_check():
    """Health check endpoint"""
    libreoffice_path = find_libreoffice()
    
    return jsonify({
        'status': 'healthy',
        'service': 'libreoffice-docx-to-pdf-converter',
        'version': '1.0.0',
        'libreoffice_available': libreoffice_path is not None,
        'libreoffice_path': libreoffice_path,
        'platform': platform.system(),
        'features': ['formatting-preservation', 'libreoffice-conversion', 'high-quality-output']
    })

@app.route('/convert', methods=['POST'])
def convert_docx_to_pdf():
    """Convert DOCX to PDF using LibreOffice"""
    try:
        # Validate request
        if 'file' not in request.files:
            return jsonify({'success': False, 'error': 'No file provided'}), 400
        
        file = request.files['file']
        
        # Validate file
        try:
            validate_file(file)
        except Exception as e:
            return jsonify({'success': False, 'error': str(e)}), 400
        
        # Create temporary files
        with tempfile.NamedTemporaryFile(delete=False, suffix='.docx') as temp_docx:
            file.save(temp_docx.name)
            docx_path = temp_docx.name
        
        with tempfile.NamedTemporaryFile(delete=False, suffix='.pdf') as temp_pdf:
            pdf_path = temp_pdf.name
        
        try:
            # Convert using LibreOffice
            convert_with_libreoffice(docx_path, pdf_path)
            
            # Check if PDF was created successfully
            if not os.path.exists(pdf_path):
                raise Exception("PDF file was not created")
            
            # Get file size
            pdf_size = os.path.getsize(pdf_path)
            if pdf_size == 0:
                raise Exception("Generated PDF is empty")
            
            # Read PDF and convert to base64
            with open(pdf_path, 'rb') as pdf_file:
                pdf_data = pdf_file.read()
                pdf_base64 = base64.b64encode(pdf_data).decode('utf-8')
            
            # Clean up temporary files
            os.unlink(docx_path)
            os.unlink(pdf_path)
            
            return jsonify({
                'success': True,
                'filename': file.filename,
                'method': 'libreoffice-conversion',
                'message': 'Conversion completed successfully with full formatting preserved',
                'pdf_data': pdf_base64,
                'pdf_size': pdf_size,
                'libreoffice_used': True
            })
            
        except Exception as e:
            # Clean up temporary files on error
            if os.path.exists(docx_path):
                os.unlink(docx_path)
            if os.path.exists(pdf_path):
                os.unlink(pdf_path)
            raise e
            
    except Exception as e:
        return jsonify({
            'success': False,
            'error': f'Conversion failed: {str(e)}'
        }), 500

@app.route('/convert-json', methods=['POST'])
def convert_docx_to_pdf_json():
    """Convert DOCX to PDF using LibreOffice (JSON endpoint)"""
    return convert_docx_to_pdf()

@app.route('/test', methods=['GET'])
def test_conversion():
    """Test the conversion service"""
    try:
        libreoffice_path = find_libreoffice()
        
        if not libreoffice_path:
            return jsonify({
                'success': False,
                'error': 'LibreOffice not found. Please install LibreOffice first.',
                'installation_guide': {
                    'windows': 'https://www.libreoffice.org/download/download/',
                    'linux': 'sudo apt-get install libreoffice',
                    'mac': 'https://www.libreoffice.org/download/download/'
                }
            }), 400
        
        return jsonify({
            'success': True,
            'message': 'LibreOffice converter is ready',
            'libreoffice_path': libreoffice_path,
            'platform': platform.system()
        })
        
    except Exception as e:
        return jsonify({
            'success': False,
            'error': f'Test failed: {str(e)}'
        }), 500

@app.route('/', methods=['GET'])
def index():
    """Service information"""
    return jsonify({
        'service': 'LibreOffice DOCX to PDF Converter',
        'version': '1.0.0',
        'description': 'High-quality DOCX to PDF conversion using LibreOffice',
        'endpoints': {
            'health': '/health',
            'convert': '/convert',
            'convert_json': '/convert-json',
            'test': '/test'
        },
        'features': [
            'Full formatting preservation',
            'High-quality output',
            'Support for complex documents',
            'Image and table preservation',
            'Professional-grade conversion'
        ]
    })

if __name__ == '__main__':
    print("LibreOffice DOCX to PDF Converter")
    print("==================================")
    
    # Check LibreOffice availability
    libreoffice_path = find_libreoffice()
    if libreoffice_path:
        print(f"✅ LibreOffice found at: {libreoffice_path}")
    else:
        print("❌ LibreOffice not found!")
        print("Please install LibreOffice for best results:")
        print("- Windows: https://www.libreoffice.org/download/download/")
        print("- Linux: sudo apt-get install libreoffice")
        print("- Mac: https://www.libreoffice.org/download/download/")
    
    print(f"🌐 Platform: {platform.system()}")
    
    # Check if running in production mode
    import sys
    if len(sys.argv) > 1 and sys.argv[1] == '--unix-socket':
        # Unix socket mode for production
        socket_path = '/tmp/doc_converter.sock'
        print(f"🔒 Starting Unix socket server on {socket_path}")
        app.run(host='unix://' + socket_path, debug=False)
    else:
        # HTTP mode for development
        print("🚀 Starting HTTP server on http://0.0.0.0:5000")
        print("📖 Available endpoints:")
        print("   - GET  /health - Health check")
        print("   - POST /convert - Convert DOCX to PDF")
        print("   - GET  /test - Test conversion")
        print("   - GET  / - Service information")
        app.run(host='0.0.0.0', port=5000, debug=False)
