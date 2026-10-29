<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: Login.html');
    exit;
}
$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html dir="ltr" lang="en" xml:lang="en" class="yui3-js-enabled">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Dashboard | CLASS-IPB</title>
    <link rel="shortcut icon" href="https://class.ipb.ac.id/pluginfile.php/1/core_admin/favicon/64x64/1769503579/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f0f2f5;
            color: #333;
        }
        
        .navbar {
            background: white;
            border-bottom: 1px solid #e8e8e8;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            padding: 0.75rem 0;
        }
        
        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1rem;
            font-weight: 500;
        }
        
        .navbar-brand img {
            height: 40px;
        }
        
        .navbar-nav {
            margin-left: 3rem;
        }
        
        .navbar-nav .nav-link {
            color: #666 !important;
            font-size: 0.95rem;
            margin-right: 1.5rem;
            padding: 0.5rem 0 !important;
            border-bottom: 2px solid transparent;
            transition: all 0.3s;
        }
        
        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            color: #007bff !important;
            border-bottom-color: #007bff;
        }
        
        .user-menu {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            position: relative;
        }
        
        .user-avatar {
            width: 35px;
            height: 35px;
            background: #007bff;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        .user-dropdown {
            display: none;
            position: absolute;
            top: 45px;
            right: 0;
            background: white;
            border: 1px solid #e8e8e8;
            border-radius: 0.25rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            min-width: 200px;
            z-index: 100;
        }
        
        .user-menu:hover .user-dropdown {
            display: block;
        }
        
        .user-dropdown a {
            display: block;
            padding: 0.75rem 1rem;
            color: #666;
            text-decoration: none;
            border-bottom: 1px solid #f0f0f0;
            font-size: 0.9rem;
            transition: all 0.2s;
        }
        
        .user-dropdown a:last-child {
            border-bottom: none;
        }
        
        .user-dropdown a:hover {
            background: #f5f5f5;
            color: #007bff;
            padding-left: 1.25rem;
        }
        
        .drawer {
            width: 250px;
            background: white;
            min-height: calc(100vh - 60px);
            border-right: 1px solid #e8e8e8;
            padding: 1.5rem 0;
            position: fixed;
            left: 0;
            top: 60px;
            overflow-y: auto;
        }
        
        .drawer a {
            display: block;
            padding: 0.75rem 1.5rem;
            color: #666;
            text-decoration: none;
            border-left: 3px solid transparent;
            transition: all 0.3s;
            font-size: 0.9rem;
        }
        
        .drawer a:hover {
            background: #f5f5f5;
            color: #007bff;
        }
        
        .drawer a.active {
            background: #f0f8ff;
            color: #007bff;
            border-left-color: #007bff;
            font-weight: 600;
        }
        
        .main-content {
            margin-left: 250px;
            margin-top: 60px;
            padding: 2rem;
        }
        
        .page-header {
            margin-bottom: 1.5rem;
        }
        
        .page-title {
            font-size: 1.75rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        
        .welcome-msg {
            font-size: 1.1rem;
            color: #666;
            margin-top: 1rem;
        }
        
        .search-sort-container {
            background: white;
            padding: 1rem;
            border-radius: 0.25rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        
        .search-sort-container .form-control,
        .search-sort-container .form-select {
            max-width: 200px;
        }
        
        .course-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }
        
        .course-card {
            background: white;
            border-radius: 0.25rem;
            overflow: hidden;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: all 0.3s;
        }
        
        .course-card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }
        
        .course-image {
            height: 160px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
            overflow: hidden;
        }
        
        .course-card:nth-child(2) .course-image {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .course-card:nth-child(3) .course-image {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        .course-card:nth-child(4) .course-image {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }
        
        .course-body {
            padding: 1.25rem;
        }
        
        .course-code {
            font-size: 0.75rem;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 600;
        }
        
        .course-name {
            font-size: 1.05rem;
            font-weight: 500;
            margin: 0.5rem 0;
            color: #333;
        }
        
        .course-category {
            font-size: 0.85rem;
            color: #999;
            margin-bottom: 0.75rem;
        }
        
        .progress-section {
            margin-top: 1rem;
        }
        
        .progress {
            height: 4px;
            background: #e9ecef;
            border-radius: 2px;
        }
        
        .progress-bar {
            background: #007bff;
            border-radius: 2px;
        }
        
        .progress-text {
            font-size: 0.8rem;
            color: #666;
            margin-top: 0.25rem;
        }
        
        @media (max-width: 768px) {
            .drawer {
                display: none;
            }
            
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
            
            .course-grid {
                grid-template-columns: 1fr;
            }
            
            .navbar-nav {
                display: none !important;
            }
        }
    </style>
</head>
<body id="page-my-index" class="limit-width format-site path-my chrome dir-ltr lang-en_us yui-skin-sam yui3-skin-sam class-ipb-ac-id pagelayout-mydashboard course-1 context-456278 theme uses-drawers drawer-open-index jsenabled">
    
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top">
        <div class="container-fluid">
            <a href="#" class="navbar-brand">
                <img src="./Dashboard _ CLASS-IPB_files/Logo-IPB-baru-360x360.png" alt="CLASS-IPB" class="logo">
            </a>
            
            <ul class="navbar-nav ms-4">
                <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
                <li class="nav-item"><a class="nav-link active" href="#">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="#">My courses</a></li>
            </ul>
            
            <div class="ms-auto user-menu">
                <div class="user-avatar"><?php echo strtoupper(substr($username, 0, 1)); ?></div>
                <span style="color: #333; font-weight: 500;"><?php echo htmlspecialchars($username); ?></span>
                <div class="user-dropdown">
                    <a href="#"><i class="fas fa-user me-2"></i>Profile</a>
                    <a href="#"><i class="fas fa-star me-2"></i>Grades</a>
                    <a href="#"><i class="fas fa-calendar me-2"></i>Calendar</a>
                    <a href="#"><i class="fas fa-file me-2"></i>Private files</a>
                    <a href="#"><i class="fas fa-cog me-2"></i>Preferences</a>
                    <a href="Login.html"><i class="fas fa-sign-out-alt me-2"></i>Log out</a>
                </div>
            </div>
        </div>
    </nav>
    
    <div class="drawer">
        <a href="#" class="active"><i class="fas fa-home me-2"></i>Home</a>
        <a href="#"><i class="fas fa-chart-line me-2"></i>Dashboard</a>
        <a href="#"><i class="fas fa-book me-2"></i>My courses</a>
        <a href="#"><i class="fas fa-file me-2"></i>Private files</a>
        <a href="#"><i class="fas fa-chart-bar me-2"></i>Reports</a>
    </div>
    
    <main class="main-content">
        <header class="page-header mb-3">
            <h1 class="page-title">Dashboard</h1>
            <span class="welcome-msg">Hi, <?php echo htmlspecialchars($username); ?>! 👋</span>
        </header>
        
        <div class="card mb-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-4 mb-2 mb-md-0">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" id="search-courses" class="form-control" placeholder="Search courses...">
                        </div>
                    </div>
                    <div class="col-md-4 mb-2 mb-md-0">
                        <select id="sort-courses" class="form-select">
                            <option value="fullname">Sort by course name</option>
                            <option value="shortname">Sort by short name</option>
                            <option value="lastaccessed">Sort by last accessed</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select id="view-mode" class="form-select">
                            <option value="card">Card</option>
                            <option value="list">List</option>
                            <option value="summary">Summary</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="course-grid">
            <div class="course-card">
                <div class="course-image"><i class="fas fa-code"></i></div>
                <div class="course-body">
                    <div class="course-code">TPL2102</div>
                    <div class="course-name">Perancangan Web</div>
                    <div class="course-category">Teknologi Rekayasa Perangkat Lunak</div>
                    <div class="progress-section">
                        <div class="progress">
                            <div class="progress-bar" style="width: 15%"></div>
                        </div>
                        <div class="progress-text">1% complete</div>
                    </div>
                </div>
            </div>
            
            <div class="course-card">
                <div class="course-image"><i class="fas fa-film"></i></div>
                <div class="course-body">
                    <div class="course-code">TPL2105</div>
                    <div class="course-name">Teknologi Multimedia</div>
                    <div class="course-category">Teknologi Rekayasa Perangkat Lunak</div>
                    <div class="progress-section">
                        <div class="progress">
                            <div class="progress-bar" style="width: 45%"></div>
                        </div>
                        <div class="progress-text">45% complete</div>
                    </div>
                </div>
            </div>
            
            <div class="course-card">
                <div class="course-image"><i class="fas fa-sitemap"></i></div>
                <div class="course-body">
                    <div class="course-code">TPL2106</div>
                    <div class="course-name">Algoritma dan Struktur Data</div>
                    <div class="course-category">Teknologi Rekayasa Perangkat Lunak</div>
                    <div class="progress-section">
                        <div class="progress">
                            <div class="progress-bar" style="width: 68%"></div>
                        </div>
                        <div class="progress-text">68% complete</div>
                    </div>
                </div>
            </div>
            
            <div class="course-card">
                <div class="course-image"><i class="fas fa-database"></i></div>
                <div class="course-body">
                    <div class="course-code">TPL2107</div>
                    <div class="course-name">Basis Data</div>
                    <div class="course-category">Teknologi Rekayasa Perangkat Lunak</div>
                    <div class="progress-section">
                        <div class="progress">
                            <div class="progress-bar" style="width: 30%"></div>
                        </div>
                        <div class="progress-text">30% complete</div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        const courses = [
            {id: 1, code: 'TPL2102', fullname: 'Perancangan Web', shortname: 'Web', category: 'Teknologi Rekayasa Perangkat Lunak', progress: 15, icon: 'fa-code'},
            {id: 2, code: 'TPL2105', fullname: 'Teknologi Multimedia', shortname: 'Multimedia', category: 'Teknologi Rekayasa Perangkat Lunak', progress: 45, icon: 'fa-film'},
            {id: 3, code: 'TPL2106', fullname: 'Algoritma dan Struktur Data', shortname: 'Algoritma', category: 'Teknologi Rekayasa Perangkat Lunak', progress: 68, icon: 'fa-sitemap'},
            {id: 4, code: 'TPL2107', fullname: 'Basis Data', shortname: 'Database', category: 'Teknologi Rekayasa Perangkat Lunak', progress: 30, icon: 'fa-database'}
        ];
        
        let currentSort = 'fullname';
        let currentView = 'card';
        let filteredCourses = [...courses];
        
        const searchInput = document.getElementById('search-courses');
        const sortSelect = document.getElementById('sort-courses');
        const viewSelect = document.getElementById('view-mode');
        const courseGrid = document.querySelector('.course-grid');
        
        function renderCourses() {
            courseGrid.innerHTML = '';
            
            if (currentView === 'card') {
                courseGrid.style.display = 'grid';
                courseGrid.style.gridTemplateColumns = 'repeat(auto-fill, minmax(300px, 1fr))';
                courseGrid.className = 'course-grid';
            } else if (currentView === 'list') {
                courseGrid.style.display = 'flex';
                courseGrid.style.flexDirection = 'column';
                courseGrid.className = 'course-list';
            } else if (currentView === 'summary') {
                courseGrid.style.display = 'flex';
                courseGrid.style.flexDirection = 'column';
                courseGrid.className = 'course-summary';
            }
            
            filteredCourses.forEach((course, idx) => {
                const colors = ['#667eea 0%, #764ba2 100%', '#f093fb 0%, #f5576c 100%', '#4facfe 0%, #00f2fe 100%', '#43e97b 0%, #38f9d7 100%'];
                const gradient = colors[idx % colors.length];
                
                let html = '';
                
                if (currentView === 'card') {
                    html = `
                        <div class="course-card">
                            <div class="course-image" style="background: linear-gradient(135deg, ${gradient});">
                                <i class="fas ${course.icon}"></i>
                            </div>
                            <div class="course-body">
                                <div class="course-code">${course.code}</div>
                                <div class="course-name">${course.fullname}</div>
                                <div class="course-category">${course.category}</div>
                                <div class="progress-section">
                                    <div class="progress">
                                        <div class="progress-bar" style="width: ${course.progress}%"></div>
                                    </div>
                                    <div class="progress-text">${course.progress}% complete</div>
                                </div>
                            </div>
                        </div>
                    `;
                } else if (currentView === 'list') {
                    html = `
                        <div class="course-list-item">
                            <div class="list-item-content">
                                <div class="list-item-header">
                                    <span class="course-code">${course.code}</span>
                                    <span class="course-name ms-2">${course.fullname}</span>
                                </div>
                                <div class="list-item-footer">
                                    <span class="course-category">${course.category}</span>
                                    <div style="margin-left: auto; width: 200px;">
                                        <div class="progress" style="height: 4px;">
                                            <div class="progress-bar" style="width: ${course.progress}%"></div>
                                        </div>
                                        <small>${course.progress}% complete</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                } else if (currentView === 'summary') {
                    html = `
                        <div class="course-summary-item">
                            <div class="summary-header">
                                <span class="course-code">${course.code}</span>
                                <span class="course-name">${course.fullname}</span>
                                <span class="summary-progress">${course.progress}%</span>
                            </div>
                        </div>
                    `;
                }
                
                courseGrid.innerHTML += html;
            });
        }
        
        function filterAndSort() {
            const searchTerm = searchInput.value.toLowerCase();
            
            filteredCourses = courses.filter(course => 
                course.fullname.toLowerCase().includes(searchTerm) ||
                course.code.toLowerCase().includes(searchTerm) ||
                course.shortname.toLowerCase().includes(searchTerm)
            );
            
            if (currentSort === 'fullname') {
                filteredCourses.sort((a, b) => a.fullname.localeCompare(b.fullname));
            } else if (currentSort === 'shortname') {
                filteredCourses.sort((a, b) => a.shortname.localeCompare(b.shortname));
            } else if (currentSort === 'lastaccessed') {
                filteredCourses.sort((a, b) => b.id - a.id);
            }
            
            renderCourses();
        }
        
        searchInput.addEventListener('input', filterAndSort);
        
        sortSelect.addEventListener('change', (e) => {
            currentSort = e.target.value;
            filterAndSort();
        });
        
        viewSelect.addEventListener('change', (e) => {
            currentView = e.target.value;
            renderCourses();
        });
        
        renderCourses();
    </script>
    
    <style>
        .course-list-item {
            background: white;
            padding: 1.25rem;
            border-bottom: 1px solid #e8e8e8;
            display: flex;
            align-items: center;
            transition: all 0.3s;
        }
        
        .course-list-item:hover {
            background: #f5f5f5;
        }
        
        .list-item-content {
            width: 100%;
        }
        
        .list-item-header {
            display: flex;
            align-items: center;
            margin-bottom: 0.75rem;
        }
        
        .list-item-footer {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .course-summary-item {
            background: white;
            padding: 1rem;
            border-bottom: 1px solid #e8e8e8;
            display: flex;
            align-items: center;
        }
        
        .summary-header {
            display: flex;
            align-items: center;
            width: 100%;
            gap: 1.5rem;
        }
        
        .summary-progress {
            font-weight: 600;
            color: #007bff;
            margin-left: auto;
            min-width: 60px;
            text-align: right;
        }
    </style>
</body>
</html>
