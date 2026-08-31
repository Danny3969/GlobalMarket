#!/usr/bin/env python3
"""
DEPLOY SCRIPT - GLOBAL MARKET GM
Automatiza el despliegue de archivos hacia el servidor cPanel/FTP.
"""
from ftplib import FTP
import os

HOST = '23.145.120.19'
USER = 'jumofres'
PASS = 'c0KrKa7d&Fjo4EN;1'
REMOTE_ROOT = '/globalmarket-gm.com'

local_dir = os.path.dirname(os.path.abspath(__file__))

print(f"🚀 Conectando a {HOST} vía FTP...")
ftp = FTP()
ftp.connect(HOST, 21, timeout=15)
ftp.login(USER, PASS)
print("✅ Autenticado con éxito.")

# Crear carpetas remotas
def make_remote_dirs(r_dir):
    parts = r_dir.strip('/').split('/')
    cur = REMOTE_ROOT
    for p in parts:
        cur += '/' + p
        try:
            ftp.mkd(cur)
            print(f"📁 Carpeta creada: {cur}")
        except Exception:
            pass

make_remote_dirs('assets')
make_remote_dirs('assets/images')

print(f"\n📤 Subiendo archivos a {REMOTE_ROOT}...")

for root, dirs, files in os.walk(local_dir):
    # Evitar carpetas de git y temporales
    if '.git' in root or '__pycache__' in root:
        continue
    for f in files:
        if f.startswith('.') and f != '.htaccess':
            continue
        if f in ['deploy.py', 'MEMORY.md', 'README.md', '.gitignore']:
            continue
        local_path = os.path.join(root, f)
        rel_path = os.path.relpath(local_path, local_dir)
        remote_path = REMOTE_ROOT + '/' + rel_path.replace('\\', '/')
        
        with open(local_path, 'rb') as fp:
            ftp.storbinary(f'STOR {remote_path}', fp)
            file_size = os.path.getsize(local_path)
            print(f"  ✓ Subido: {rel_path} ({file_size/1024:.1f} KB)")

print("\n🎉 Despliegue completado. Sitio en vivo: https://globalmarket-gm.com")
ftp.quit()
