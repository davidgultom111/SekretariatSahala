import matplotlib.pyplot as plt
import matplotlib.patches as mpatches
from matplotlib.patches import FancyBboxPatch, FancyArrowPatch
import matplotlib.patheffects as pe
import numpy as np

fig, ax = plt.subplots(1, 1, figsize=(18, 11))
ax.set_xlim(0, 18)
ax.set_ylim(0, 11)
ax.axis('off')
fig.patch.set_facecolor('white')

# ─── helper functions ───────────────────────────────────────────────
def box(ax, x, y, w, h, fc, ec, lw=1.5, radius=0.25, alpha=1.0):
    rect = FancyBboxPatch((x, y), w, h,
                          boxstyle=f"round,pad=0,rounding_size={radius}",
                          facecolor=fc, edgecolor=ec, linewidth=lw, alpha=alpha)
    ax.add_patch(rect)

def label(ax, x, y, text, fs=9, fw='normal', color='black', ha='center', va='center'):
    ax.text(x, y, text, fontsize=fs, fontweight=fw, color=color,
            ha=ha, va=va, wrap=True)

def section_title(ax, x, y, text, fs=10, color='black'):
    ax.text(x, y, text, fontsize=fs, fontweight='bold', color=color,
            ha='center', va='center')

def arrow(ax, x1, y1, x2, y2, color='#333333', lw=1.5, head=0.25):
    ax.annotate('', xy=(x2, y2), xytext=(x1, y1),
                arrowprops=dict(arrowstyle=f'->', color=color,
                                lw=lw, mutation_scale=head*60))

def double_arrow(ax, x1, y1, x2, y2, color='#333333', lw=1.5, label_text='', label_fs=8):
    ax.annotate('', xy=(x2, y2), xytext=(x1, y1),
                arrowprops=dict(arrowstyle='<->', color=color,
                                lw=lw, mutation_scale=15))
    if label_text:
        mx, my = (x1+x2)/2, (y1+y2)/2
        ax.text(mx, my+0.18, label_text, fontsize=label_fs, ha='center', color=color)

# ─── colour palette ─────────────────────────────────────────────────
C_SERVER_BG  = '#EFF6FF'   # light blue
C_SERVER_BD  = '#1D4ED8'
C_CLIENT_BG  = '#F0FDF4'   # light green
C_CLIENT_BD  = '#15803D'
C_DB_BG      = '#FFF7ED'   # light orange
C_DB_BD      = '#C2410C'
C_BOX_API    = '#DBEAFE'
C_BOX_CTRL   = '#E0E7FF'
C_BOX_SVC    = '#EDE9FE'
C_BOX_MODEL  = '#FCE7F3'
C_BOX_WEB    = '#D1FAE5'
C_BOX_MOB    = '#DCFCE7'
C_BOX_AUTH   = '#FEF9C3'
C_ARROW      = '#374151'

# ═══════════════════════════════════════════════════════════════════
# TITLE
# ═══════════════════════════════════════════════════════════════════
ax.text(9, 10.55, 'Arsitektur Sistem Sekretariat Jemaat GPdI',
        fontsize=15, fontweight='bold', ha='center', va='center', color='#1e293b')
ax.text(9, 10.18, 'REST API — Laravel 11 · Sanctum Auth · DomPDF · Queue (DB Driver)',
        fontsize=8.5, ha='center', va='center', color='#64748b')

# ═══════════════════════════════════════════════════════════════════
# SERVER SIDE panel  (left)
# ═══════════════════════════════════════════════════════════════════
box(ax, 0.3, 0.4, 9.1, 9.4, C_SERVER_BG, C_SERVER_BD, lw=2, radius=0.35)
section_title(ax, 4.85, 9.55, 'SERVER - SIDE', fs=11, color=C_SERVER_BD)

# -- Sanctum Auth block --
box(ax, 0.7, 7.9, 2.6, 1.2, C_BOX_AUTH, '#CA8A04', lw=1.3, radius=0.2)
label(ax, 2.0, 8.75, 'Auth Middleware', fs=8.5, fw='bold', color='#78350F')
label(ax, 2.0, 8.38, 'Laravel Sanctum', fs=8, color='#78350F')
label(ax, 2.0, 8.07, 'Bearer Token · CheckRole', fs=7.5, color='#92400E')

# -- API Layer block --
box(ax, 3.8, 7.9, 5.3, 1.2, C_BOX_API, '#1D4ED8', lw=1.3, radius=0.2)
label(ax, 6.45, 8.75, 'API Layer  —  routes/api.php', fs=8.5, fw='bold', color='#1e3a8a')
label(ax, 6.45, 8.38, 'Public  |  Member (auth)  |  Admin (auth + role:admin)', fs=8, color='#1e3a8a')
label(ax, 6.45, 8.07, 'BaseController  ·  JSON response shape', fs=7.5, color='#1e40af')

# -- Controllers block --
box(ax, 0.7, 6.35, 8.4, 1.25, C_BOX_CTRL, '#4338CA', lw=1.3, radius=0.2)
label(ax, 4.9, 7.28, 'Controllers  (app/Http/Controllers/API/)', fs=8.5, fw='bold', color='#312E81')
ctrl_items = [
    'MemberAuthController', 'MemberApiController', 'Admin/MemberController',
    'Admin/LetterController', 'Admin/PengajuanController',
    'Admin/JadwalController', 'Admin/GaleriController', 'Admin/PengumumanController',
]
cols = 4
for i, c in enumerate(ctrl_items):
    cx = 1.2 + (i % cols) * 2.05
    cy = 6.93 if i < cols else 6.57
    label(ax, cx, cy, c, fs=7, color='#4338CA')

# -- Services block --
box(ax, 0.7, 4.9, 8.4, 1.15, C_BOX_SVC, '#7C3AED', lw=1.3, radius=0.2)
label(ax, 4.9, 5.73, 'Service Layer  (app/Services/)', fs=8.5, fw='bold', color='#4C1D95')
svc_items = ['LetterTemplateService', 'generateLetterNumber()', 'generatePDF() · DomPDF',
             'MemberObserver', 'id_jemaat auto-gen', 'Queue (DB Driver)']
for i, s in enumerate(svc_items):
    cx = 1.2 + (i % 3) * 2.75
    cy = 5.38 if i < 3 else 5.05
    label(ax, cx, cy, s, fs=7.5, color='#5B21B6')

# -- Models block --
box(ax, 0.7, 3.45, 8.4, 1.15, C_BOX_MODEL, '#BE185D', lw=1.3, radius=0.2)
label(ax, 4.9, 4.28, 'Models  (app/Models/)  — Eloquent ORM + SoftDeletes', fs=8.5, fw='bold', color='#831843')
model_items = ['Member', 'Letter', 'PengajuanSurat',
               'LetterNumberCounter', 'JadwalPelayanan', 'GaleriFoto', 'Pengumuman']
for i, m in enumerate(model_items):
    cx = 1.0 + i * 1.2
    cy = 3.78
    label(ax, cx, cy, m, fs=7.5, color='#9D174D')

# -- Database block --
box(ax, 1.8, 0.7, 5.8, 2.45, C_DB_BG, C_DB_BD, lw=1.8, radius=0.25)
label(ax, 4.7, 2.82, 'Database  (MySQL)', fs=9, fw='bold', color=C_DB_BD)
db_tables = ['members', 'letters', 'pengajuan_surat',
             'letter_number_counters', 'jadwal_pelayanan',
             'galeri_foto', 'pengumuman', 'personal_access_tokens']
for i, t in enumerate(db_tables):
    cx = 2.1 + (i % 4) * 1.42
    cy = 2.38 if i < 4 else 1.9
    small_box_fc = '#FED7AA' if i % 2 == 0 else '#FFEDD5'
    box(ax, cx-0.6, cy-0.18, 1.22, 0.36, small_box_fc, C_DB_BD, lw=0.8, radius=0.08)
    label(ax, cx, cy, t, fs=6.8, color='#7C2D12')
label(ax, 4.7, 1.3, 'Migrasi · Seeder · Eloquent SoftDeletes · ON DELETE CASCADE', fs=7.2, color='#9A3412')

# ═══════════════════════════════════════════════════════════════════
# CLIENT SIDE panel  (right)
# ═══════════════════════════════════════════════════════════════════
box(ax, 9.8, 0.4, 7.9, 9.4, C_CLIENT_BG, C_CLIENT_BD, lw=2, radius=0.35)
section_title(ax, 13.75, 9.55, 'CLIENT - SIDE', fs=11, color=C_CLIENT_BD)

# -- Admin Web Frontend --
box(ax, 10.15, 7.6, 7.25, 1.65, C_BOX_WEB, C_CLIENT_BD, lw=1.3, radius=0.2)
label(ax, 13.77, 8.97, 'Admin Web Frontend', fs=9, fw='bold', color='#14532D')
label(ax, 13.77, 8.62, 'Nuxt.js  +  Laravel Blade (port 8001)', fs=8.5, color='#166534')
label(ax, 13.77, 8.28, 'Halaman: Login · Member · Surat · Jadwal · Galeri · Pengumuman · Pengajuan', fs=7.5, color='#15803D')
label(ax, 13.77, 7.93, 'Konsumsi REST API · Import dari GitHub Repository', fs=7.5, color='#15803D')

# -- Mobile App --
box(ax, 10.15, 5.9, 3.4, 1.45, C_BOX_MOB, '#047857', lw=1.3, radius=0.2)
label(ax, 11.85, 7.02, 'Mobile App', fs=9, fw='bold', color='#064E3B')
label(ax, 11.85, 6.68, 'Android / iOS', fs=8, color='#065F46')
label(ax, 11.85, 6.38, 'Member self-service\n(profile · pengajuan · letters)', fs=7.5, color='#047857')

# -- Public Website --
box(ax, 13.9, 5.9, 3.5, 1.45, '#F0FDFA', '#0F766E', lw=1.3, radius=0.2)
label(ax, 15.65, 7.02, 'Website Publik', fs=9, fw='bold', color='#134E4A')
label(ax, 15.65, 6.68, 'Nuxt.js / Next.js', fs=8, color='#0F766E')
label(ax, 15.65, 6.38, 'Jadwal · Galeri · Pengumuman\n(unauthenticated read-only)', fs=7.5, color='#0F766E')

# -- GitHub Repository --
box(ax, 10.15, 3.9, 3.4, 1.7, '#F8FAFC', '#334155', lw=1.3, radius=0.2)
label(ax, 11.85, 5.25, 'GitHub Repository', fs=9, fw='bold', color='#1E293B')
label(ax, 11.85, 4.88, 'Backend (Laravel API)', fs=8, color='#334155')
label(ax, 11.85, 4.56, 'Frontend (Nuxt.js)', fs=8, color='#334155')
label(ax, 11.85, 4.20, 'Import via git clone / deploy', fs=7.5, color='#475569')

# -- OpenAPI Docs (Scramble) --
box(ax, 13.9, 3.9, 3.5, 1.7, '#FDF4FF', '#7E22CE', lw=1.3, radius=0.2)
label(ax, 15.65, 5.25, 'API Docs', fs=9, fw='bold', color='#581C87')
label(ax, 15.65, 4.88, 'Scramble (dedoc)', fs=8, color='#6B21A8')
label(ax, 15.65, 4.56, '/docs/api', fs=8.5, fw='bold', color='#7E22CE')
label(ax, 15.65, 4.22, 'Auto-generated OpenAPI\nspec from controllers', fs=7.5, color='#7E22CE')

# -- PDF Output --
box(ax, 10.15, 0.7, 7.25, 2.85, '#FFF1F2', '#BE123C', lw=1.5, radius=0.2)
label(ax, 13.77, 3.22, 'Output  —  Surat Resmi (PDF)', fs=9, fw='bold', color='#881337')
pdf_types = [
    'Surat Tugas Pelayanan (TP)',  'Surat Pengantar (SP)',
    'Keterangan Jemaat Aktif (KJA)', 'Surat Nilai Sekolah (NS)',
    'Pengajuan Baptisan (PB)',      'Penyerahan Anak (PA)',
    'Pengajuan Pernikahan (PP)',
]
for i, p in enumerate(pdf_types):
    cx = 10.55 + (i % 2) * 3.6
    cy = 2.78 - (i // 2) * 0.46
    box(ax, cx-0.1, cy-0.18, 3.3, 0.36, '#FFE4E6', '#BE123C', lw=0.7, radius=0.07)
    label(ax, cx+1.55, cy, p, fs=7.2, color='#9F1239')
label(ax, 13.77, 0.98, 'DomPDF  ·  Blade template (letter/print.blade.php)  ·  NNN/GPdI/SA/ABBREV/YEAR', fs=7.2, color='#BE123C')

# ═══════════════════════════════════════════════════════════════════
# HTTP / CLOUD in the MIDDLE
# ═══════════════════════════════════════════════════════════════════
# Draw a cloud-like oval
from matplotlib.patches import Ellipse
cloud = Ellipse((9.05, 5.85), width=1.95, height=1.1,
                facecolor='#F1F5F9', edgecolor='#64748B', linewidth=1.5, zorder=3)
ax.add_patch(cloud)
ax.text(9.05, 6.05, 'HTTP', fontsize=8, fontweight='bold',
        ha='center', va='center', color='#1e293b', zorder=4)
ax.text(9.05, 5.78, 'Request', fontsize=7.5, ha='center', va='center', color='#334155', zorder=4)
ax.text(9.05, 5.52, 'Response', fontsize=7.5, ha='center', va='center', color='#334155', zorder=4)
ax.text(9.05, 5.26, '(JSON)', fontsize=7, ha='center', va='center', color='#64748B', zorder=4)

# ═══════════════════════════════════════════════════════════════════
# ARROWS  (internal server flow)
# ═══════════════════════════════════════════════════════════════════
# Auth → API
arrow(ax, 3.3, 7.9, 3.9, 9.1, color='#CA8A04', lw=1.4, head=0.2)
# API → Controllers
double_arrow(ax, 4.9, 7.9, 4.9, 7.6, color='#4338CA', lw=1.4, label_text='', label_fs=7)
# Controllers → Services
double_arrow(ax, 4.9, 6.35, 4.9, 6.05, color='#7C3AED', lw=1.4)
# Services → Models
double_arrow(ax, 4.9, 4.9, 4.9, 4.6, color='#BE185D', lw=1.4)
# Models → DB
double_arrow(ax, 4.9, 3.45, 4.9, 3.15, color=C_DB_BD, lw=1.4, label_text='SQL / Eloquent', label_fs=7.5)

# ─── Server → Cloud → Client arrows ────────────────────────────────
# API → cloud
double_arrow(ax, 9.1, 8.5, 9.1, 6.4, color='#334155', lw=1.6, label_text='GET/POST/PUT/DELETE', label_fs=7.5)
# cloud → Admin web
ax.annotate('', xy=(10.15, 8.42), xytext=(9.55, 6.4),
            arrowprops=dict(arrowstyle='<->', color='#166534', lw=1.5, mutation_scale=13))
# cloud → Mobile
ax.annotate('', xy=(10.15, 6.55), xytext=(9.55, 5.7),
            arrowprops=dict(arrowstyle='<->', color='#047857', lw=1.5, mutation_scale=13))
# cloud → Public website
ax.annotate('', xy=(13.9, 6.55), xytext=(9.55, 5.7),
            arrowprops=dict(arrowstyle='<->', color='#0F766E', lw=1.5, mutation_scale=13))

# Admin web → GitHub
arrow(ax, 11.85, 7.6, 11.85, 5.6, color='#334155', lw=1.3, head=0.18)
# Services → PDF output
ax.annotate('', xy=(13.0, 3.55), xytext=(9.1, 4.92),
            arrowprops=dict(arrowstyle='->', color='#BE123C', lw=1.5, mutation_scale=13))
ax.text(11.1, 4.55, 'PDF Generate', fontsize=7.5, color='#BE123C', ha='center', rotation=20)

# API → Scramble docs
ax.annotate('', xy=(13.9, 4.75), xytext=(9.1, 8.5),
            arrowprops=dict(arrowstyle='->', color='#7E22CE', lw=1.2,
                            mutation_scale=12, connectionstyle='arc3,rad=0.35'))
ax.text(11.9, 7.0, '/docs/api\n(auto)', fontsize=7, color='#7E22CE', ha='center')

# ═══════════════════════════════════════════════════════════════════
# Legend
# ═══════════════════════════════════════════════════════════════════
leg_x, leg_y = 0.35, 0.32
legend_items = [
    (C_BOX_AUTH, '#CA8A04', 'Authentication'),
    (C_BOX_API, '#1D4ED8', 'API Layer'),
    (C_BOX_CTRL, '#4338CA', 'Controllers'),
    (C_BOX_SVC, '#7C3AED', 'Services'),
    (C_BOX_MODEL, '#BE185D', 'Models'),
    (C_DB_BG, C_DB_BD, 'Database'),
    (C_BOX_WEB, C_CLIENT_BD, 'Frontend'),
    ('#FFF1F2', '#BE123C', 'PDF Output'),
]
for i, (fc, ec, lbl) in enumerate(legend_items):
    bx = leg_x + i * 2.17
    box(ax, bx, leg_y-0.1, 0.45, 0.22, fc, ec, lw=1, radius=0.05)
    ax.text(bx + 0.55, leg_y+0.02, lbl, fontsize=6.8, va='center', color='#374151')

plt.tight_layout(pad=0.3)
out_path = r'C:\laragon\www\sekretariat\docs\architecture-diagram.png'
plt.savefig(out_path, dpi=180, bbox_inches='tight', facecolor='white')
print(f'Saved: {out_path}')
plt.close()
