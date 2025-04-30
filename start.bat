@echo off

REM Activate the virtual environment
call C:\xampp\htdocs\whatsapp_automation\backend\venv\Scripts\activate.bat

REM Start the Python backend
start cmd /k "cd c:\xampp\htdocs\whatsapp_automation\backend && python app.py"

REM Start the PHP server (optional, if not using Apache)
cd C:\path\to\frontend
php -S localhost:3000
