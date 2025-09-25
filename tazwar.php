<?php
/**
 * Portfolio — Monim Tazwar (single file)
 * Stack: HTML + CSS + JS + PHP (contact form)
 */

// Production-safe error settings
error_reporting(E_ALL & ~E_NOTICE);
ini_set('log_errors', '1');
ini_set('display_errors', '0');

/* ==================== CONFIG (User-filled) ==================== */
$CONFIG = [
  'site_name'     => 'Monim Tazwar',
  'role'          => 'Front-End Developer | Software Engineer',
  'location'      => 'Chattogram / Dhaka, Bangladesh',
  'phone_public'  => '01755010278',
  'phone_public_2'=> '01533575491',
  'email_public'  => 'tazwarmonim@gmail.com', // shown on page
  'owner_email'   => 'tazwarmonim@gmail.com', // used for mail(); change if needed
  'profile_pic'   => 'Media.jpeg',            // ensure this file exists here
  'social' => [
    ['label' => 'GitHub',    'url' => 'https://github.com/Tazwar91'],
    ['label' => 'LinkedIn',  'url' => 'https://www.linkedin.com/in/monim-tazwar-ba08512b6/'],
    ['label' => 'Instagram', 'url' => 'https://www.instagram.com/monim_tazwar/'],
    ['label' => 'Facebook',  'url' => 'https://www.facebook.com/monim.tazwar.2024'],
  ],
  'skills' => [
    'Programming Languages' => ['C','C++','C#','PHP','JavaScript','Python','R','SQL'],
    'Front-End' => ['HTML','CSS','Responsive Web Design','UI/UX Principles','DOM Manipulation','Form Validation'],
    'Software & Tools' => ['Oracle','MySQL','PostgreSQL','Visual Studio','Code::Blocks','Notepad++','PyCharm','RStudio'],
    'Operating Systems' => ['Windows 7 / 10 / 11'],
    'Productivity' => ['Microsoft Word','Excel','PowerPoint'],
  ],
  /* 
   * Projects: Linked ones FIRST (with buttons), then unlinked (no buttons).
   */
  'projects' => [
    [
      'title' => 'OmniRoute — Multi-Terrain Vehicle Simulation',
      'tags'  => ['OpenGL','C++','Blender','Computer Graphics'],
      'desc'  => '3D simulation of a vehicle transitioning across land, air, and underwater with animated environments.',
      'link'  => 'https://github.com/Tazwar91/Graphics', // linked
    ],
    [
      'title' => 'ConnectHub — Social Networking Platform',
      'tags'  => ['PHP','MySQL','MVC','Auth'],
      'desc'  => 'Full-stack social app: authentication, posts, likes, comments, messaging, friends & responsive UI.',
      'link'  => 'https://github.com/emonsafayetrid/ConnectHub', // linked
    ],
    [
      'title' => 'Sentiment Product Grouping (R)',
      'tags'  => ['R','tidyverse','text mining','clustering'],
      'desc'  => 'R project for sentiment analysis and product grouping using text mining, tokenization, and clustering.',
      'link'  => 'https://github.com/Tazwar91/sentiment-product-grouping', // linked
    ],
    [
      'title' => 'School Management System',
      'tags'  => ['C++','OOP','File Handling'],
      'desc'  => 'Console system to manage student records, staff, classes, and fees with CRUD and menu-driven UI.',
      // no link -> no button
    ],
    [
      'title' => 'MediTech — Sales & Inventory Management',
      'tags'  => ['C#','Windows Forms','Desktop App'],
      'desc'  => 'Tracks product sales, purchases, and stock with summaries and intuitive UI.',
      // no link -> no button
    ],
  ],
  'experience' => [
    [
      'company' => 'ProAms Company Limited',
      'role'    => 'Field Supervisor',
      'period'  => '2023',
      'bullets' => [
        'Conducted customer satisfaction surveys for Habib Bank Limited to gather service feedback.',
        'Communicated with clients to identify improvements and document suggestions.',
        'Bridged communication between customers and the bank to support service development.'
      ]
    ],
  ],
  'education' => [
    [
      'school' => 'American International University – Bangladesh (AIUB)',
      'degree' => 'BSc in Computer Science',
      'period' => '2022 — Running'
    ],
    [
      'school' => 'Chattogram Cantonment Public College',
      'degree' => 'Higher Secondary Certificate',
      'period' => '2020'
    ],
    [
      'school' => 'Chattogram Collegiate School',
      'degree' => 'Secondary School Certificate',
      'period' => '2018'
    ]
  ],
  'achievements' => [
    'Finalist — Poster Competition (Spring 2022–23), Dept. of Physics, AIUB',
    "Dean’s List — Spring 2023–24 & Fall 2024–25 at AIUB",
    'Front-End Development Training (Dec 2024 – Jan 2025), IIT, Jahangirnagar University — EDGE Project (BCC, ICT Division)'
  ],
  'languages' => [
    'English — reading, writing, speaking, listening',
    'Bengali — reading, writing, speaking, listening'
  ],
];

/* ================= CONTACT FORM (PHP) ================= */
$alert = null; // UI message for form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_form'])) {
  $name    = trim($_POST['name']    ?? '');
  $email   = trim($_POST['email']   ?? '');
  $message = trim($_POST['message'] ?? '');
  $honey   = trim($_POST['website'] ?? ''); // honeypot: must be empty

  if ($honey !== '') {
    $alert = ['type' => 'error', 'text' => 'Form failed verification.'];
  } elseif ($name === '' || $email === '' || $message === '') {
    $alert = ['type' => 'error', 'text' => 'Please fill in all required fields.'];
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $alert = ['type' => 'error', 'text' => 'Please enter a valid email address.'];
  } else {
    // Save message to a local log (fallback if mail is disabled on host)
    $logLine = sprintf(
      "%s\t%s\t%s\t%s\t%s\n",
      date('c'), $_SERVER['REMOTE_ADDR'] ?? 'unknown', $name, $email, str_replace(["\r","\n"], [' ',' '], $message)
    );
    @file_put_contents(__DIR__ . '/messages.log', $logLine, FILE_APPEND | LOCK_EX);

    // Try to send mail (requires configured mail on host)
    $to      = $CONFIG['owner_email'];
    $subject = 'New portfolio contact from ' . $name;
    $headers = "From: {$CONFIG['site_name']} <no-reply@" . ($_SERVER['HTTP_HOST'] ?? 'localhost') . ">\r\n" .
               "Reply-To: $email\r\n" .
               "Content-Type: text/plain; charset=UTF-8\r\n";
    $body    = "Name: $name\nEmail: $email\nIP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . "\n\nMessage:\n$message\n";

    $sent = @mail($to, $subject, $body, $headers);
    $alert = $sent
      ? ['type' => 'success', 'text' => 'Thanks! Your message has been sent.']
      : ['type' => 'success', 'text' => 'Thanks! Your message has been saved. I\'ll reply soon.'];
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($CONFIG['site_name']) ?> — Portfolio</title>
  <meta name="description" content="<?= htmlspecialchars($CONFIG['role']) ?> — Portfolio of <?= htmlspecialchars($CONFIG['site_name']) ?>" />
  <style>
    :root{
      --bg: #0b0e14;
      --card: #0e131c;
      --text: #f3f4f6;
      --muted:#9aa3b2;
      --brand:#38bdf8;   /* brighter for contrast */
      --accent:#22c55e;
      --danger:#ef4444;
      --ring: rgba(56,189,248,.35);
      --border: #1f2937;
      --shadow: 0 8px 30px rgba(0,0,0,.35);
    }
    *{box-sizing:border-box}
    html,body{
      margin:0;padding:0;
      font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, 'Helvetica Neue', Arial, 'Noto Sans', sans-serif;
      background:var(--bg);color:var(--text);line-height:1.6;
      -webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;
    }
    a{color:var(--brand);text-decoration:none}
    a:hover{text-decoration:underline}
    .container{max-width:1180px;margin:0 auto;padding:0 1.2rem}
    header{position:sticky;top:0;z-index:50;background:linear-gradient(180deg, rgba(11,14,20,.9), rgba(11,14,20,.4), rgba(11,14,20,0)); backdrop-filter:saturate(140%) blur(6px)}
    nav{display:flex;align-items:center;justify-content:space-between;padding:0.9rem 0}
    .logo{font-weight:800;letter-spacing:.2px}
    .nav-links{display:flex;gap:1rem;align-items:center}
    .nav-links a{color:var(--text);opacity:.9;font-weight:500}
    .btn{display:inline-flex;align-items:center;gap:.5rem;border:1px solid var(--border);background:#0b1220;padding:.7rem 1.05rem;border-radius:.9rem;cursor:pointer;box-shadow:var(--shadow);transition:transform .12s ease, background .12s ease, border-color .12s ease;color:var(--text);font-weight:600}
    .btn:hover{border-color:#2c3a4d;background:#0f1830;transform:translateY(-1px)}
    .pill{display:inline-block;padding:.28rem .66rem;border:1px solid var(--border);border-radius:999px;color:var(--muted);font-size:.86rem;background:#0b1220}
    .grid{display:grid;gap:1.2rem}
    .hero{padding:3rem 0 2rem;display:grid;grid-template-columns:1fr;gap:1.5rem;align-items:center}
    .hero h1{font-size:clamp(2rem,4.2vw,3.25rem);margin:.25rem 0 0.2rem}
    .sub{color:var(--muted);max-width:72ch}
    .card{background:linear-gradient(180deg, var(--card), #0b1220);border:1px solid var(--border);border-radius:1.1rem;padding:1.1rem;box-shadow:var(--shadow)}
    .kpis{display:grid;grid-template-columns:repeat(4,1fr);gap:1.2rem}
    .kpi{background:#0b1220;border:1px solid var(--border);border-radius:1rem;padding:1rem;text-align:center}
    .kpi .v{font-size:1.35rem;font-weight:800}
    .section{padding:2rem 0}
    h2{font-size:1.6rem;margin:0 0 1rem}
    .tags{display:flex;flex-wrap:wrap;gap:.5rem}
    .tag{background:#0b1220;border:1px solid var(--border);border-radius:.6rem;padding:.32rem .68rem;font-size:.9rem;color:#d9e4f2}
    .project{display:flex;flex-direction:column;gap:.6rem}
    .project a.btn{align-self:flex-start}
    .timeline{display:grid;gap:1rem}
    .item{display:grid;gap:.25rem;border-left:3px solid #223049;padding-left:.95rem}
    .item .meta{color:var(--muted);font-size:.95rem}
    .skills{display:grid;grid-template-columns:repeat(3,1fr);gap:1.2rem}
    .skills .card ul{margin:.5rem 0 0;padding-left:1.2rem}
    form{display:grid;gap:.9rem}
    label{display:block;margin-bottom:.25rem;color:#cbd5e1}
    input,textarea{width:100%;background:#0b1220;border:1px solid var(--border);color:var(--text);padding:1rem;border-radius:.8rem;font-size:1rem}
    input:focus,textarea:focus{outline:2px solid var(--ring);border-color:#2a4158}
    textarea{min-height:150px;resize:vertical}
    .alert{padding:.9rem 1rem;border-radius:.8rem;border:1px solid;margin-bottom:1rem}
    .alert.success{border-color:#1f4f33;background:#0d1a13;color:#e7f8ec}
    .alert.error{border-color:#5b2a2a;background:#180e0e;color:#ffe3e3}
    footer{padding:2rem 0;color:var(--muted)}

    /* Profile image — BIGGER */
    .hero-wrap{display:grid;grid-template-columns:1fr;gap:1.25rem;align-items:center}
    .avatar{width:240px;height:240px;border-radius:50%;object-fit:cover;border:3px solid #2a3244;box-shadow:0 0 0 6px rgba(56,189,248,.09)}
    .hero-actions{display:flex;gap:.66rem;flex-wrap:wrap;margin-top:.85rem}

    /* Responsive */
    @media (min-width: 900px){
      .hero{grid-template-columns:260px 1fr}
      .hero-wrap{grid-template-columns:1fr}
    }
    @media (max-width: 1100px){
      .skills{grid-template-columns:1fr 1fr}
      .kpis{grid-template-columns:repeat(2,1fr)}
    }
    @media (max-width: 640px){
      nav .nav-links{display:none}
      .menu-btn{display:inline-flex}
      .kpis{grid-template-columns:1fr}
      .skills{grid-template-columns:1fr}
      .avatar{width:200px;height:200px}
    }
    .menu-btn{display:none}
    .mobile-menu{display:none;flex-direction:column;gap:.75rem;padding-bottom:.75rem}
    .mobile-menu.open{display:flex}

    /* Light theme (improved visibility; dark remains same) */
    body.light{
      --bg:#f8fafc;
      --card:#ffffff;
      --text:#0f172a;
      --muted:#334155;
      --brand:#0ea5e9;
      --accent:#16a34a;
      --danger:#b91c1c;
      --ring:rgba(14,165,233,.35);
      --border:#cbd5e1;
      --shadow:0 6px 22px rgba(2, 6, 23, .08);
    }
    body.light header{background:linear-gradient(180deg, rgba(255,255,255,.9), rgba(255,255,255,.6), rgba(255,255,255,0));}
    body.light .card{background:#ffffff;border-color:var(--border);}
    body.light .kpi{background:#f8fafc;border-color:#e2e8f0;}
    body.light .tag{background:#f1f5f9;border-color:#cbd5e1;color:#0f172a;}
    body.light .btn{background:#ffffff;border-color:#cbd5e1;color:#0f172a;}
    body.light .btn:hover{background:#f8fafc;border-color:#94a3b8;}
    body.light input, body.light textarea{
      background:#ffffff;border-color:var(--border);color:#0f172a;
    }
    body.light label{color:#334155;}
    body.light a{color:#0284c7;}
    body.light .nav-links a{color:#0f172a;opacity:.9;}
    body.light .item{border-left-color:#e2e8f0}
    body.light .pill{border-color:#e2e8f0;background:#ffffff;color:#334155}
  </style>
</head>
<body>
  <header>
    <div class="container">
      <nav>
        <div class="logo"><?= htmlspecialchars($CONFIG['site_name']) ?></div>
        <div class="nav-links" id="navLinks">
          <a href="#about">About</a>
          <a href="#skills">Skills</a>
          <a href="#projects">Projects</a>
          <a href="#experience">Experience</a>
          <a href="#education">Education</a>
          <a href="#achievements">Achievements</a>
          <a href="#languages">Languages</a>
          <a href="#contact">Contact</a>
          <button class="btn" id="themeToggle" type="button" aria-label="Toggle theme">🌓 Theme</button>
        </div>
        <button class="btn menu-btn" id="menuBtn" type="button" aria-expanded="false" aria-controls="mobileMenu">☰ Menu</button>
      </nav>
      <div id="mobileMenu" class="mobile-menu container" aria-label="Mobile">
        <a href="#about" onclick="closeMenu()">About</a>
        <a href="#skills" onclick="closeMenu()">Skills</a>
        <a href="#projects" onclick="closeMenu()">Projects</a>
        <a href="#experience" onclick="closeMenu()">Experience</a>
        <a href="#education" onclick="closeMenu()">Education</a>
        <a href="#achievements" onclick="closeMenu()">Achievements</a>
        <a href="#languages" onclick="closeMenu()">Languages</a>
        <a href="#contact" onclick="closeMenu()">Contact</a>
      </div>
    </div>
  </header>

  <main class="container">
    <section class="hero">
      <!-- Profile Picture -->
      <img class="avatar" src="<?= htmlspecialchars($CONFIG['profile_pic']) ?>" alt="Profile picture of <?= htmlspecialchars($CONFIG['site_name']) ?>" onerror="this.style.display='none'">
      <div class="hero-wrap">
        <div>
          <span class="pill">👋 Hello! I am</span>
          <h1><?= htmlspecialchars($CONFIG['site_name']) ?> — <?= htmlspecialchars($CONFIG['role']) ?></h1>
          <p class="sub">Based in <?= htmlspecialchars($CONFIG['location']) ?>. I build reliable, fast, and user-friendly applications.</p>
          <div class="hero-actions">
            <?php foreach ($CONFIG['social'] as $s): ?>
              <a class="btn" target="_blank" rel="noopener" href="<?= htmlspecialchars($s['url']) ?>">🔗 <?= htmlspecialchars($s['label']) ?></a>
            <?php endforeach; ?>
            <a class="btn" href="#contact">✉️ Contact</a>
          </div>
        </div>
      </div>
      <div class="kpis section" style="grid-column:1/-1">
        <div class="kpi"><div class="v">Student at AIUB</div><div class="sub">BSc in CSE</div></div>
        <div class="kpi"><div class="v">5</div><div class="sub">Highlighted Projects</div></div>
        <div class="kpi"><div class="v">7+</div><div class="sub">Programming Languages</div></div>
        <div class="kpi"><div class="v">ENG/BN</div><div class="sub">Bilingual</div></div>
      </div>
    </section>

    <section id="about" class="section">
      <h2>About</h2>
      <div class="card">
        <p>I am Monim Tazwar, a Computer Science student with a strong interest in developing innovative software solutions. My focus lies in creating applications that combine functionality with usability, covering areas such as management systems, desktop applications, and computer graphics simulations.</p>

        <p>I have hands-on experience with C, C++, C#, PHP, JavaScript, HTML, and CSS, along with proficiency in tools like Visual Studio, Oracle, and PyCharm. My projects reflect both technical problem-solving and design-driven thinking, allowing me to approach challenges from multiple perspectives.</p>

        <p>I am motivated by continuous learning and eager to apply my skills in professional environments where I can contribute to impactful projects while expanding my expertise.</p>
      </div>
    </section>

    <section id="skills" class="section">
      <h2>Skills</h2>
      <div class="skills">
        <?php foreach ($CONFIG['skills'] as $group => $items): ?>
          <div class="card">
            <strong><?= htmlspecialchars($group) ?></strong>
            <ul>
              <?php foreach ($items as $it): ?><li><?= htmlspecialchars($it) ?></li><?php endforeach; ?>
            </ul>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <section id="projects" class="section">
      <h2>Projects</h2>
      <div class="grid" style="grid-template-columns:repeat(auto-fill,minmax(280px,1fr))">
        <?php foreach ($CONFIG['projects'] as $p): ?>
          <article class="card project">
            <h3 style="margin:0"><?= htmlspecialchars($p['title']) ?></h3>
            <div class="tags">
              <?php foreach ($p['tags'] as $t): ?><span class="tag">#<?= htmlspecialchars($t) ?></span><?php endforeach; ?>
            </div>
            <p class="sub" style="margin:0"><?= htmlspecialchars($p['desc']) ?></p>
            <?php if (!empty($p['link'])): ?>
              <a class="btn" target="_blank" rel="noopener" href="<?= htmlspecialchars($p['link']) ?>">Open project →</a>
            <?php endif; ?>
          </article>
        <?php endforeach; ?>
      </div>
    </section>

    <section id="experience" class="section">
      <h2>Experience</h2>
      <div class="timeline">
        <?php foreach ($CONFIG['experience'] as $e): ?>
          <div class="card item">
            <strong><?= htmlspecialchars($e['role']) ?></strong>
            <div class="meta"><?= htmlspecialchars($e['company']) ?> • <?= htmlspecialchars($e['period']) ?></div>
            <ul style="margin:.4rem 0 0 1.2rem">
              <?php foreach ($e['bullets'] as $b): ?><li><?= htmlspecialchars($b) ?></li><?php endforeach; ?>
            </ul>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <section id="education" class="section">
      <h2>Education</h2>
      <div class="timeline">
        <?php foreach ($CONFIG['education'] as $ed): ?>
          <div class="card item">
            <strong><?= htmlspecialchars($ed['degree']) ?></strong>
            <div class="meta"><?= htmlspecialchars($ed['school']) ?> • <?= htmlspecialchars($ed['period']) ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <section id="achievements" class="section">
      <h2>Achievements & Certifications</h2>
      <div class="card">
        <ul style="margin:.4rem 0 0 1.2rem">
          <?php foreach ($CONFIG['achievements'] as $a): ?><li><?= htmlspecialchars($a) ?></li><?php endforeach; ?>
        </ul>
      </div>
    </section>

    <section id="languages" class="section">
      <h2>Language Proficiency</h2>
      <div class="card">
        <ul style="margin:.4rem 0 0 1.2rem">
          <?php foreach ($CONFIG['languages'] as $l): ?><li><?= htmlspecialchars($l) ?></li><?php endforeach; ?>
        </ul>
      </div>
    </section>

    <section id="contact" class="section">
      <h2>Contact</h2>
      <?php if ($alert): ?>
        <div class="alert <?= htmlspecialchars($alert['type']) ?>"><?= htmlspecialchars($alert['text']) ?></div>
      <?php endif; ?>
      <div class="card">
        <p class="sub" style="margin-top:0">
          📞 <a href="tel:<?= htmlspecialchars($CONFIG['phone_public']) ?>"><?= htmlspecialchars($CONFIG['phone_public']) ?></a>
          <?php if (!empty($CONFIG['phone_public_2'])): ?>
            &nbsp;•&nbsp;
            📞 <a href="tel:<?= htmlspecialchars($CONFIG['phone_public_2']) ?>"><?= htmlspecialchars($CONFIG['phone_public_2']) ?></a>
          <?php endif; ?>
          &nbsp;•&nbsp;
          ✉️ <a href="mailto:<?= htmlspecialchars($CONFIG['email_public']) ?>"><?= htmlspecialchars($CONFIG['email_public']) ?></a>
        </p>
        <form method="post" action="#contact" novalidate>
          <input type="hidden" name="contact_form" value="1" />
          <div class="grid" style="grid-template-columns:1fr 1fr">
            <div>
              <label for="name">Name*</label>
              <input id="name" name="name" required placeholder="Your name" />
            </div>
            <div>
              <label for="email">Email*</label>
              <input id="email" name="email" type="email" required placeholder="you@example.com" />
            </div>
          </div>
          <!-- Honeypot field (hidden from users) -->
          <div style="position:absolute;left:-9999px;opacity:0" aria-hidden="true">
            <label for="website">Website</label>
            <input id="website" name="website" tabindex="-1" autocomplete="off" />
          </div>
          <div>
            <label for="message">Message*</label>
            <textarea id="message" name="message" required placeholder="How can I help you?"></textarea>
          </div>
          <button class="btn" type="submit">Send Message</button>
        </form>
      </div>
    </section>
  </main>

  <footer class="container">
    <div class="card" style="display:flex;align-items:center;justify-content:space-between;gap:1rem">
      <span>© <span id="year"></span> <?= htmlspecialchars($CONFIG['site_name']) ?> — All rights reserved.</span>
      <span class="sub">Built with HTML • CSS • JS • PHP</span>
    </div>
  </footer>

  <script>
    // Mobile menu toggle
    const menuBtn   = document.getElementById('menuBtn');
    const mobileNav = document.getElementById('mobileMenu');
    function closeMenu(){ mobileNav.classList.remove('open'); menuBtn.setAttribute('aria-expanded','false'); }
    menuBtn?.addEventListener('click', () => {
      const isOpen = mobileNav.classList.toggle('open');
      menuBtn.setAttribute('aria-expanded', String(isOpen));
    });

    // Theme toggle with localStorage
    const themeToggle = document.getElementById('themeToggle');
    const savedTheme = localStorage.getItem('theme');
    if(savedTheme === 'light'){ document.body.classList.add('light'); }
    themeToggle?.addEventListener('click', () => {
      document.body.classList.toggle('light');
      localStorage.setItem('theme', document.body.classList.contains('light') ? 'light' : 'dark');
    });

    // Smooth scroll for same-page anchors (basic)
    document.querySelectorAll('a[href^="#"]').forEach(a => {
      a.addEventListener('click', e => {
        const id = a.getAttribute('href');
        if(id.length > 1){
          e.preventDefault();
          document.querySelector(id)?.scrollIntoView({behavior:'smooth',block:'start'});
        }
      });
    });

    // Current year
    document.getElementById('year').textContent = new Date().getFullYear();
  </script>
</body>
</html>
