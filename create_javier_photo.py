#!/usr/bin/env python3
# Script para crear foto de perfil de Javier Edgar Sandy Da Cruz

from PIL import Image, ImageDraw
import os

# Ruta donde guardar la imagen
output_path = os.path.join(os.path.dirname(__file__), 'public', 'img', 'javier.jpg')

# Crear imagen 400x400 con gradiente azul profesional
img = Image.new('RGB', (400, 400), color=(39, 107, 130))
draw = ImageDraw.Draw(img)

# Marco profesional
draw.rectangle([30, 30, 370, 370], outline=(100, 150, 180), width=4)

# Círculo superior para cabeza (avatar profesional)
draw.ellipse([80, 40, 320, 200], fill=(80, 140, 170))

# Rectángulo inferior para cuerpo
draw.rectangle([60, 180, 340, 380], fill=(60, 120, 150))

# Agregar decoración con líneas
draw.line([(50, 200), (350, 200)], fill=(100, 150, 180), width=2)

# Guardar
img.save(output_path, 'JPEG', quality=90)
print(f"✓ Foto de perfil creada: {output_path}")
