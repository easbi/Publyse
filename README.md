# Publyse - Publication Review System

<p align="center">
  <img src="https://via.placeholder.com/400x100/3B82F6/FFFFFF?text=PUBLYSE" alt="Publyse Logo" width="400">
</p>

<p align="center">
<img src="https://img.shields.io/badge/Laravel-10.x-FF2D20?style=flat&logo=laravel" alt="Laravel Version">
<img src="https://img.shields.io/badge/Vue.js-3.x-4FC08D?style=flat&logo=vue.js" alt="Vue.js Version">
<img src="https://img.shields.io/badge/PDF.js-3.11-FF6B35?style=flat&logo=mozilla" alt="PDF.js Version">
<img src="https://img.shields.io/badge/Tailwind-3.x-06B6D4?style=flat&logo=tailwindcss" alt="Tailwind CSS">
<img src="https://img.shields.io/badge/License-MIT-green.svg" alt="License">
</p>

## About Publyse

Publyse is a sophisticated **Publication Review System** designed to streamline the academic and professional publication review process. Built with modern web technologies, it provides an intuitive platform for reviewers to examine, annotate, and collaborate on PDF documents with precision and efficiency.

## Key Features

### 🔍 **Advanced PDF Review System**
- **Interactive PDF Viewer** with zoom controls (25% - 500%)
- **Scale-Aware Annotations** - comments maintain accurate positioning across different zoom levels
- **Dual Annotation Types**: Point markers and area selections
- **Real-time Navigation** with page controls and keyboard shortcuts

### 💬 **Smart Comment Management**
- **Hierarchical Comments** with reply threads
- **Status Tracking** (Open/Done) with role-based permissions
- **Scale-Aware Navigation** - automatically adjusts zoom to comment's original scale
- **Visual Scale Indicators** with color-coded badges
- **Pagination** for performance optimization

### 🎨 **Modern User Interface**
- **Responsive Design** optimized for desktop and tablet
- **Split-Screen Layout** (5:7 ratio) - PDF viewer and comment sidebar
- **Real-time Feedback** with toast notifications
- **Accessibility Features** with ARIA labels and keyboard navigation

### 🚀 **Technical Excellence**
- **Vue 3 Composition API** for reactive components
- **Laravel 10** backend with RESTful API
- **MySQL Database** with optimized queries
- **PDF.js Integration** for client-side PDF rendering
- **Tailwind CSS** for utility-first styling

## System Requirements

- **PHP** >= 8.1
- **Laravel** >= 10.0
- **Node.js** >= 16.0
- **MySQL** >= 8.0
- **Modern Browser** with JavaScript enabled

## Installation

### 1. Clone Repository
```bash
git clone https://github.com/your-org/publyse.git
cd publyse
```

### 2. Backend Setup
```bash
# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database in .env file
DB_DATABASE=publyse
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 3. Database Migration
```bash
# Run migrations
php artisan migrate

# Seed sample data (optional)
php artisan db:seed
```

### 4. Frontend Setup
```bash
# Install Node.js dependencies
npm install

# Build assets for development
npm run dev

# Or build for production
npm run build
```

### 5. Start Development Server
```bash
# Laravel development server
php artisan serve

# Vite development server (separate terminal)
npm run dev
```

## Usage Guide

### For Reviewers

1. **Access Document**: Navigate to assigned publication
2. **Set Optimal Zoom**: Choose appropriate zoom level for detailed review
3. **Add Comments**: 
   - Use **Point Tool** for specific location comments
   - Use **Area Tool** for section-based feedback
4. **Navigate Comments**: Click comment badges to jump to exact locations with original zoom
5. **Manage Status**: Mark comments as "Done" when addressed

### For Authors

1. **View Feedback**: Browse comments in organized sidebar
2. **Scale-Aware Navigation**: Comments automatically adjust zoom for accurate positioning
3. **Reply to Comments**: Engage in threaded discussions
4. **Track Progress**: Monitor comment status (Open/Done)

## API Endpoints

### Comments
```
GET    /api/comments              # List comments
POST   /api/comments              # Create comment
PUT    /api/comments/{id}         # Update comment
DELETE /api/comments/{id}         # Delete comment
PATCH  /api/comments/{id}/status  # Update status
```

### Documents
```
GET    /api/documents/{id}        # Get document details
GET    /api/documents/{id}/pdf    # Download PDF
```

## Database Schema

### Comments Table
```sql
CREATE TABLE comments (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    document_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    page_number INT NOT NULL,
    type ENUM('point', 'area') NOT NULL,
    position LONGTEXT, -- JSON coordinates
    content TEXT NOT NULL,
    status ENUM('open', 'done') DEFAULT 'open',
    created_at_scale DECIMAL(4,2) DEFAULT 1.00, -- Scale awareness
    page_dimensions LONGTEXT, -- JSON page size
    original_position LONGTEXT, -- Backup position
    parent_id BIGINT NULL, -- For replies
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

## Vue Components Architecture

```
PdfReviewer.vue (Main Container)
├── PdfViewerPanel.vue (PDF Display)
│   ├── PDF Canvas Rendering
│   ├── Annotation Layer (SVG)
│   ├── Zoom Controls
│   └── Navigation Controls
└── CommentManagement.vue (Sidebar)
    ├── Filter Controls
    ├── Comment List with Pagination
    ├── Scale-Aware Badges
    └── Reply Management
```

## Scale-Aware Technology

Publyse features innovative **scale-aware annotations** that maintain positional accuracy across different zoom levels:

- **Position Normalization**: Coordinates stored relative to 100% scale
- **Dynamic Adjustment**: Real-time position calculation for current zoom
- **Visual Indicators**: Color-coded badges show original zoom level
- **Auto-Navigation**: Clicking comments adjusts zoom to original scale

## Development

### Code Style
- **PHP**: PSR-12 coding standard
- **JavaScript**: ESLint with Vue 3 configuration
- **CSS**: Tailwind utility classes with scoped components

### Testing
```bash
# PHP Unit Tests
php artisan test

# JavaScript Tests
npm run test
```

### Building for Production
```bash
# Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Build frontend assets
npm run build
```

## Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open Pull Request

### Contribution Guidelines
- Follow existing code style and conventions
- Add tests for new features
- Update documentation as needed
- Ensure cross-browser compatibility

## Security

Report security vulnerabilities to: security@publyse.com

All security issues will be promptly addressed following responsible disclosure practices.

## License

Publyse is open-source software licensed under the [MIT License](https://opensource.org/licenses/MIT).

## Support & Documentation

- **Documentation**: [docs.publyse.com](https://docs.publyse.com)
- **Issues**: [GitHub Issues](https://github.com/easbi/Publyse/issues)
- **Discussions**: [GitHub Discussions](https://github.com/easbi/Publyse/discussions)
- **Email**: easbi@bps.go.id
- 
## Roadmap

- [ ] Real-time collaboration features
- [ ] Advanced annotation tools (highlighting, drawing)
- [ ] Export functionality (PDF with comments)
- [ ] Mobile application
- [ ] Integration with popular academic platforms
- [ ] Advanced analytics and reporting
- [ ] Multi-language support

---

<p align="center">
  Built with ❤️ for the academic and professional community
</p>

<p align="center">
  <a href="https://laravel.com">Laravel</a> •
  <a href="https://vuejs.org">Vue.js</a> •
  <a href="https://mozilla.github.io/pdf.js/">PDF.js</a> •
  <a href="https://tailwindcss.com">Tailwind CSS</a>
</p>
