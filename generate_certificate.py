#!/usr/bin/env python3
"""
Certificate Generator Script
Generates a PDF certificate in landscape format (11" x 8.5")
with user information overlaid on the certificate template.
"""

import sys
from reportlab.lib.pagesizes import letter, landscape
from reportlab.pdfgen import canvas
from reportlab.lib.units import inch
from reportlab.pdfbase import pdfmetrics
from reportlab.pdfbase.ttfonts import TTFont
from PIL import Image
import os

def generate_certificate(output_path, user_name, cert_number, issue_date, lessons_completed, avg_quiz_score, avg_sim_score, template_path):
    """
    Generate a certificate PDF with user information overlaid on template image.
    
    Args:
        output_path: Path where PDF will be saved
        user_name: Full name of the certificate recipient
        cert_number: Certificate number (e.g., CERT-2024-ABCD1234)
        issue_date: Issue date string (e.g., "January 29, 2025")
        lessons_completed: Number of lessons completed
        avg_quiz_score: Average quiz score percentage
        avg_sim_score: Average simulation score percentage
        template_path: Path to the certificate template PNG image
    """
    
    # Create canvas with landscape letter size (11" x 8.5")
    page_width, page_height = landscape(letter)  # 792 x 612 points
    c = canvas.Canvas(output_path, pagesize=landscape(letter))
    
    # Add the certificate template image as background
    # Make sure it covers the entire page
    c.drawImage(template_path, 0, 0, width=page_width, height=page_height, preserveAspectRatio=False)
    
    # Set up font - using Helvetica Bold for the name (matches certificate style)
    c.setFont("Helvetica-Bold", 36)
    c.setFillColorRGB(0.12, 0.50, 0.36)  # Dark teal/green color matching certificate theme
    
    # Draw the user's name centered in the blank space below "PROUDLY PRESENTS TO"
    # This is approximately at y=330 (measured from certificate layout)
    name_y_position = 285
    text_width = c.stringWidth(user_name, "Helvetica-Bold", 36)
    name_x_position = (page_width - text_width) / 2
    c.drawString(name_x_position, name_y_position, user_name)
    
    # Now add the certificate details below the main paragraph
    # Starting at approximately y=170 (below the paragraph text)
    details_start_y = 125
    c.setFont("Helvetica", 10)
    c.setFillColorRGB(0.3, 0.3, 0.3)  # Dark gray for details
    
    # Certificate Number
    cert_number_text = f"Certificate No.: {cert_number}"
    cert_number_width = c.stringWidth(cert_number_text, "Helvetica", 10)
    c.drawString((page_width - cert_number_width) / 2, details_start_y, cert_number_text)
    
    # Issue Date
    date_text = f"Issued on: {issue_date}"
    date_width = c.stringWidth(date_text, "Helvetica", 10)
    c.drawString((page_width - date_width) / 2, details_start_y - 15, date_text)
    
    # Achievement details in a compact format
    achievement_text = f"Completed {lessons_completed} lessons • Quiz Average: {avg_quiz_score}% • Simulation Average: {avg_sim_score}%"
    achievement_width = c.stringWidth(achievement_text, "Helvetica", 9)
    c.setFont("Helvetica", 9)
    c.setFillColorRGB(0.4, 0.4, 0.4)  # Lighter gray for achievement details
    c.drawString((page_width - achievement_width) / 2, details_start_y - 32, achievement_text)
    
    # Add a subtle verification line at the very bottom
    c.setFont("Helvetica", 7)
    c.setFillColorRGB(0.5, 0.5, 0.5)
    verify_text = f"Verify at: https://cyberwais.com/verify/{cert_number}"
    verify_width = c.stringWidth(verify_text, "Helvetica", 7)
    c.drawString((page_width - verify_width) / 2, 50, verify_text)
    
    # Save the PDF
    c.save()
    
    print(f"Certificate generated successfully: {output_path}")

if __name__ == "__main__":
    # Check if correct number of arguments provided
    if len(sys.argv) != 9:
        print("Usage: python generate_certificate.py <output_path> <user_name> <cert_number> <issue_date> <lessons_completed> <avg_quiz_score> <avg_sim_score> <template_path>")
        sys.exit(1)
    
    # Get arguments
    output_path = sys.argv[1]
    user_name = sys.argv[2]
    cert_number = sys.argv[3]
    issue_date = sys.argv[4]
    lessons_completed = sys.argv[5]
    avg_quiz_score = sys.argv[6]
    avg_sim_score = sys.argv[7]
    template_path = sys.argv[8]
    
    # Verify template exists
    if not os.path.exists(template_path):
        print(f"Error: Template file not found at {template_path}")
        sys.exit(1)
    
    # Generate certificate
    try:
        generate_certificate(
            output_path=output_path,
            user_name=user_name,
            cert_number=cert_number,
            issue_date=issue_date,
            lessons_completed=lessons_completed,
            avg_quiz_score=avg_quiz_score,
            avg_sim_score=avg_sim_score,
            template_path=template_path
        )
    except Exception as e:
        print(f"Error generating certificate: {str(e)}")
        sys.exit(1)
