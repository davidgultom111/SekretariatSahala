from PIL import Image, ImageDraw, ImageFont
import math, os

W, H = 1400, 800
img = Image.new('RGB', (W, H), '#FFFFFF')
draw = ImageDraw.Draw(img)

# ── try loading fonts ────────────────────────────────────────────────
def load_font(size):
    for name in ['arialbd.ttf','arial.ttf','DejaVuSans-Bold.ttf','DejaVuSans.ttf']:
        try: return ImageFont.truetype(name, size)
        except: pass
    return ImageFont.load_default()

def load_font_regular(size):
    for name in ['arial.ttf','DejaVuSans.ttf','verdana.ttf']:
        try: return ImageFont.truetype(name, size)
        except: pass
    return ImageFont.load_default()

F_TITLE   = load_font(22)
F_LABEL   = load_font(14)
F_SMALL   = load_font_regular(12)
F_TINY    = load_font_regular(11)

# ── colour palette ───────────────────────────────────────────────────
COL_BG_SERVER = '#EEF4FF'
COL_BD_SERVER = '#2563EB'
COL_BG_CLIENT = '#F0FDF4'
COL_BD_CLIENT = '#16A34A'
COL_ARROW     = '#374151'
COL_WHITE     = '#FFFFFF'
COL_GRAY      = '#6B7280'

# ── helpers ──────────────────────────────────────────────────────────
def rounded_rect(draw, x0,y0,x1,y1, r=18, fill='#FFFFFF', outline='#000000', lw=2):
    draw.rounded_rectangle([x0,y0,x1,y1], radius=r, fill=fill, outline=outline, width=lw)

def text_center(draw, cx, cy, text, font, color='#1F2937'):
    bbox = draw.textbbox((0,0), text, font=font)
    tw = bbox[2]-bbox[0]; th = bbox[3]-bbox[1]
    draw.text((cx-tw//2, cy-th//2), text, font=font, fill=color)

def arrow_h(draw, x1, y, x2, col=COL_ARROW, lw=2):
    draw.line([(x1,y),(x2,y)], fill=col, width=lw)
    d = 10
    draw.polygon([(x2,y),(x2-d,y-6),(x2-d,y+6)], fill=col)

def arrow_h_double(draw, x1, y, x2, col=COL_ARROW, lw=2):
    draw.line([(x1,y),(x2,y)], fill=col, width=lw)
    d = 10
    draw.polygon([(x2,y),(x2-d,y-6),(x2-d,y+6)], fill=col)
    draw.polygon([(x1,y),(x1+d,y-6),(x1+d,y+6)], fill=col)

def arrow_v(draw, x, y1, y2, col=COL_ARROW, lw=2):
    draw.line([(x,y1),(x,y2)], fill=col, width=lw)
    d = 10
    draw.polygon([(x,y2),(x-6,y2-d),(x+6,y2-d)], fill=col)

def arrow_v_double(draw, x, y1, y2, col=COL_ARROW, lw=2):
    draw.line([(x,y1),(x,y2)], fill=col, width=lw)
    d = 10
    draw.polygon([(x,y2),(x-6,y2-d),(x+6,y2-d)], fill=col)
    draw.polygon([(x,y1),(x-6,y1+d),(x+6,y1+d)], fill=col)

# ════════════════════════════════════════════════════════════════════
#  DRAW ICONS
# ════════════════════════════════════════════════════════════════════

def draw_monitor(draw, cx, cy, w=90, h=70, color='#1D4ED8'):
    """Simple flat monitor icon"""
    # screen body
    sw, sh = w, int(h*0.72)
    sx, sy = cx-sw//2, cy-h//2
    rounded_rect(draw, sx, sy, sx+sw, sy+sh, r=6, fill=color, outline='#1e3a8a', lw=2)
    # screen inner
    margin = 6
    draw.rectangle([sx+margin, sy+margin, sx+sw-margin, sy+sh-margin], fill='#BFDBFE')
    # stand neck
    nx = cx
    draw.rectangle([nx-4, sy+sh, nx+4, sy+sh+10], fill=color)
    # stand base
    bw = 34
    draw.rectangle([cx-bw//2, sy+sh+10, cx+bw//2, sy+sh+16], fill=color)

def draw_phone(draw, cx, cy, w=44, h=76, color='#15803D'):
    """Simple smartphone icon"""
    rx, ry = cx-w//2, cy-h//2
    rounded_rect(draw, rx, ry, rx+w, ry+h, r=8, fill=color, outline='#14532D', lw=2)
    # screen
    m = 6
    draw.rectangle([rx+m, ry+m+8, rx+w-m, ry+h-m-10], fill='#BBF7D0')
    # home button
    draw.ellipse([cx-5, ry+h-m-8, cx+5, ry+h-m+2], fill='#14532D')

def draw_globe(draw, cx, cy, r=38, color='#0369A1'):
    """Simple globe/browser icon for public website"""
    draw.ellipse([cx-r, cy-r, cx+r, cy+r], fill=color, outline='#075985', width=2)
    # horizontal lines
    for dy in [-r//3, 0, r//3]:
        chord_w = int(math.sqrt(max(0, r**2-(dy)**2)))
        draw.line([(cx-chord_w, cy+dy),(cx+chord_w, cy+dy)], fill='#7DD3FC', width=1)
    # vertical line
    draw.line([(cx, cy-r),(cx, cy+r)], fill='#7DD3FC', width=1)
    # equator
    draw.line([(cx-r, cy),(cx+r, cy)], fill='#7DD3FC', width=2)

def draw_server(draw, cx, cy, w=80, h=90, color='#374151'):
    """Rack server icon (stacked units)"""
    unit_h = 22; gap = 4
    for i in range(3):
        uy = cy - h//2 + i*(unit_h+gap)
        rounded_rect(draw, cx-w//2, uy, cx+w//2, uy+unit_h, r=4,
                     fill=color, outline='#111827', lw=2)
        # LED dot
        draw.ellipse([cx+w//2-14, uy+8, cx+w//2-6, uy+16], fill='#4ADE80')
        # slot line
        draw.rectangle([cx-w//2+8, uy+9, cx+w//2-20, uy+13], fill='#6B7280')

def draw_database(draw, cx, cy, w=80, h=70, color='#B45309'):
    """Cylinder database icon"""
    ew = w; eh = 18
    # body rect
    draw.rectangle([cx-w//2, cy-h//2+eh//2, cx+w//2, cy+h//2], fill=color, outline='#92400E', width=2)
    # bottom ellipse
    draw.ellipse([cx-w//2, cy+h//2-eh, cx+w//2, cy+h//2+4], fill='#D97706', outline='#92400E', width=2)
    # top ellipse
    draw.ellipse([cx-w//2, cy-h//2, cx+w//2, cy-h//2+eh], fill='#FCD34D', outline='#92400E', width=2)

def draw_cloud(draw, cx, cy, color='#94A3B8'):
    """Simple cloud shape"""
    r1, r2, r3 = 30, 22, 18
    # three overlapping circles
    draw.ellipse([cx-r1, cy-r1//2, cx+r1, cy+r1//2], fill=color)
    draw.ellipse([cx-r1-r2+8, cy-r2//3, cx-r1+r2+8, cy+r2*2//3+4], fill=color)
    draw.ellipse([cx+r1-r3-8, cy-r3//3, cx+r1+r3-8, cy+r3*2//3+4], fill=color)
    # flat bottom
    draw.rectangle([cx-r1-4, cy, cx+r1+4, cy+r1//2+2], fill=color)

def draw_github(draw, cx, cy, r=32, color='#1F2937'):
    """Simple GitHub icon (circle with 'git' text)"""
    draw.ellipse([cx-r, cy-r, cx+r, cy+r], fill=color, outline='#111827', width=2)
    text_center(draw, cx, cy, 'git', load_font(14), color='#FFFFFF')

# ════════════════════════════════════════════════════════════════════
#  LAYOUT  (SERVER left | cloud middle | CLIENT right)
# ════════════════════════════════════════════════════════════════════

# --- outer panels ---
# SERVER panel
rounded_rect(draw, 30, 80, 530, 740, r=22, fill=COL_BG_SERVER, outline=COL_BD_SERVER, lw=3)
text_center(draw, 280, 105, 'SERVER - SIDE', F_LABEL, color=COL_BD_SERVER)

# CLIENT panel
rounded_rect(draw, 850, 80, 1360, 740, r=22, fill=COL_BG_CLIENT, outline=COL_BD_CLIENT, lw=3)
text_center(draw, 1105, 105, 'CLIENT - SIDE', F_LABEL, color=COL_BD_CLIENT)

# ─── SERVER items ────────────────────────────────────────────────────

# 1. Database  (bottom-left)
db_cx, db_cy = 150, 620
draw_database(draw, db_cx, db_cy, color='#B45309')
text_center(draw, db_cx, db_cy+62, 'Database', F_LABEL, '#78350F')
text_center(draw, db_cx, db_cy+80, 'MySQL', F_TINY, '#92400E')

# 2. Laravel REST API server  (center-left)
sv_cx, sv_cy = 370, 400
draw_server(draw, sv_cx, sv_cy, color='#374151')
text_center(draw, sv_cx, sv_cy+68, 'REST API Server', F_LABEL, '#111827')
text_center(draw, sv_cx, sv_cy+86, 'Laravel 11 · Sanctum', F_TINY, '#374151')

# label boxes inside server panel
rounded_rect(draw, 200, 195, 500, 310, r=12, fill='#DBEAFE', outline='#2563EB', lw=2)
text_center(draw, 350, 218, 'Controllers', load_font(13), '#1E3A8A')
text_center(draw, 350, 242, 'Auth · Member · Admin', F_TINY, '#1D4ED8')
text_center(draw, 350, 263, 'Jadwal · Galeri · Pengumuman', F_TINY, '#1D4ED8')
text_center(draw, 350, 284, 'Surat · Pengajuan', F_TINY, '#1D4ED8')

rounded_rect(draw, 200, 325, 500, 395, r=12, fill='#EDE9FE', outline='#7C3AED', lw=2)
text_center(draw, 350, 345, 'Service Layer', load_font(13), '#4C1D95')
text_center(draw, 350, 367, 'LetterTemplateService · DomPDF', F_TINY, '#6D28D9')
text_center(draw, 350, 387, 'MemberObserver · Queue', F_TINY, '#6D28D9')

# arrow DB → Server
# DB and server are at different heights — draw an L-shaped connector
draw.line([(db_cx, db_cy-37),(db_cx, sv_cy+10),(sv_cx-42, sv_cy+10)], fill='#92400E', width=2)
draw.polygon([(sv_cx-42, sv_cy+10),(sv_cx-42+10, sv_cy+10-6),(sv_cx-42+10, sv_cy+10+6)], fill='#92400E')
draw.polygon([(db_cx, db_cy-37),(db_cx-6, db_cy-37+10),(db_cx+6, db_cy-37+10)], fill='#92400E')
text_center(draw, db_cx-22, (db_cy-37+sv_cy+10)//2, 'Data', F_TINY, '#92400E')

# arrow Controllers ↔ Services
arrow_v_double(draw, 350, 312, 323, col='#4338CA', lw=2)

# arrow Services ↔ Server box
arrow_v_double(draw, 370, 397, 335, col='#7C3AED', lw=2)

# ─── CLOUD in the middle ────────────────────────────────────────────
cloud_cx, cloud_cy = 692, 390
draw_cloud(draw, cloud_cx, cloud_cy, color='#CBD5E1')
# cloud border redraw for visibility
for _ in range(2):
    draw_cloud(draw, cloud_cx, cloud_cy, color='#94A3B8')

text_center(draw, cloud_cx, cloud_cy-18, 'Http Request', F_TINY, '#1E293B')
text_center(draw, cloud_cx, cloud_cy+2,  '(GET, POST,', F_TINY, '#334155')
text_center(draw, cloud_cx, cloud_cy+18, 'PUT, DELETE)', F_TINY, '#334155')
text_center(draw, cloud_cx, cloud_cy+38, 'Response (JSON)', F_TINY, '#475569')

# API ↔ cloud arrows
arrow_h_double(draw, sv_cx+42, sv_cy-10, cloud_cx-46, col='#374151', lw=2)

# ─── CLIENT items ────────────────────────────────────────────────────

# Admin Web  (top-right)
mon_cx, mon_cy = 990, 220
draw_monitor(draw, mon_cx, mon_cy, color='#1D4ED8')
text_center(draw, mon_cx, mon_cy+60, 'Admin Web', F_LABEL, '#1E3A8A')
text_center(draw, mon_cx, mon_cy+78, 'Nuxt.js (port 8001)', F_TINY, '#1D4ED8')

# Mobile App (middle-right)
mob_cx, mob_cy = 990, 420
draw_phone(draw, mob_cx, mob_cy, color='#15803D')
text_center(draw, mob_cx, mob_cy+52, 'Mobile App', F_LABEL, '#14532D')
text_center(draw, mob_cx, mob_cy+70, 'Android / iOS', F_TINY, '#166534')

# Public Website (bottom-right area)
pub_cx, pub_cy = 990, 610
draw_globe(draw, pub_cx, pub_cy, color='#0369A1')
text_center(draw, pub_cx, pub_cy+52, 'Website Publik', F_LABEL, '#075985')
text_center(draw, pub_cx, pub_cy+70, 'Jadwal · Galeri · Pengumuman', F_TINY, '#0369A1')

# GitHub Repo (far right)
gh_cx, gh_cy = 1240, 220
draw_github(draw, gh_cx, gh_cy, color='#1F2937')
text_center(draw, gh_cx, gh_cy+48, 'Repository', F_LABEL, '#111827')
text_center(draw, gh_cx, gh_cy+66, '(GitHub)', F_TINY, '#374151')

# PDF output (far right bottom)
pdf_cx, pdf_cy = 1240, 500
rounded_rect(draw, pdf_cx-65, pdf_cy-50, pdf_cx+65, pdf_cy+50, r=10,
             fill='#FFF1F2', outline='#BE123C', lw=2)
text_center(draw, pdf_cx, pdf_cy-28, 'Surat Resmi', load_font(13), '#881337')
text_center(draw, pdf_cx, pdf_cy-6,  '(PDF)', load_font(13), '#881337')
text_center(draw, pdf_cx, pdf_cy+18, 'DomPDF', F_TINY, '#BE123C')
text_center(draw, pdf_cx, pdf_cy+36, '7 jenis surat', F_TINY, '#BE123C')

# ─── CLIENT arrows ──────────────────────────────────────────────────
# cloud → Admin Web
arrow_h(draw, cloud_cx+46, mon_cy, mon_cx-48, col='#1D4ED8', lw=2)
# cloud → Mobile
arrow_h(draw, cloud_cx+46, mob_cy, mob_cx-24, col='#15803D', lw=2)
# cloud → Public web
arrow_h(draw, cloud_cx+46, pub_cy, pub_cx-42, col='#0369A1', lw=2)

# Admin Web → GitHub
arrow_h(draw, mon_cx+48, mon_cy, gh_cx-34, col='#374151', lw=2)
text_center(draw, (mon_cx+gh_cx)//2, mon_cy-16, 'Import Repository', F_TINY, '#374151')

# Service → PDF
draw.line([(sv_cx+42, sv_cy-30),(1175, sv_cy-30),(1175, pdf_cy),(pdf_cx-65, pdf_cy)],
          fill='#BE123C', width=2)
draw.polygon([(pdf_cx-65, pdf_cy),(pdf_cx-65-10, pdf_cy-6),(pdf_cx-65-10, pdf_cy+6)],
             fill='#BE123C')
text_center(draw, 1175, sv_cy-30-16, 'Generate PDF', F_TINY, '#BE123C')

# ════════════════════════════════════════════════════════════════════
#  TITLE + CAPTION
# ════════════════════════════════════════════════════════════════════
text_center(draw, W//2, 40, 'Arsitektur Web Service Sistem Sekretariat Jemaat GPdI', F_TITLE, '#0F172A')

# link hosting labels
rounded_rect(draw, 50, 708, 510, 732, r=8, fill='#DBEAFE', outline=COL_BD_SERVER, lw=1)
text_center(draw, 280, 720, 'Link Hosting : Backend.net  (Laravel REST API)', F_TINY, '#1E40AF')

rounded_rect(draw, 870, 708, 1340, 732, r=8, fill='#DCFCE7', outline=COL_BD_CLIENT, lw=1)
text_center(draw, 1105, 720, 'Link Hosting : Frontend.App  (Nuxt.js Admin)', F_TINY, '#166534')

# ── save ─────────────────────────────────────────────────────────────
out = r'C:\laragon\www\sekretariat\docs\architecture-diagram.png'
img.save(out, 'PNG', dpi=(180,180))
print('Saved:', out)
