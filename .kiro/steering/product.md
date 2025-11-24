# PurrAI Product Overview

## What is PurrAI?

**PurrAI** is a lightweight, cross-platform desktop application that brings the power of AI to your fingertips. With a cute black kitten mascot living in your system's menu bar, you can instantly access AI assistance for any task—from answering questions to analyzing images and documents.

Built with modern web technologies and packaged as a native application, PurrAI runs seamlessly on **Linux**, **Windows**, and **macOS** while maintaining a minimal footprint.

## Core Features

### Menu Bar Integration
- Always accessible from your system tray/menu bar
- Quick access to AI assistance without opening a full application
- Minimal footprint with maximum convenience

### Intelligent Chat Interface
- Natural conversation with AI powered by PrismPHP
- Multi-turn conversations with context awareness
- Real-time streaming responses

### Media & Document Support
- **Screenshot Support**: Capture and analyze screen content instantly
- **Image Analysis**: Paste images (Ctrl+V/Cmd+V) for AI processing
- **Document Attachment**: Upload and analyze documents of any type
- Support for multiple file formats

### Conversation Management
- All chats saved locally in SQLite database
- Searchable conversation history
- Resume previous conversations
- Create new chats with one click

### Visual Design
- Beautiful Glassmorphism UI style
- Dark/Light mode that adapts to system theme
- Responsive and intuitive interface
- Iconoir icon library for consistent visuals

### AI Provider Flexibility
- **Multi-Provider Support**: Configure multiple AI providers
  - OpenAI
  - Anthropic (Claude)
  - Google (Gemini)
  - Ollama (Local models)
  - And more via PrismPHP
- **Privacy-Focused**: Local Ollama models run entirely on your system
- Switch between providers and models easily

### Internationalization
- Multi-language support (i18n)
- Extensible translation system for additional languages

## Technical Architecture

### Backend Stack
- **Laravel 12**: Modern PHP framework with streamlined structure
- **Livewire 3**: Reactive components for dynamic UI
- **SQLite**: Lightweight local database for conversations
- **PrismPHP**: Unified interface for multiple AI providers

### Frontend Stack
- **Alpine.js 3**: Minimal JavaScript framework for interactivity
- **Tailwind CSS 4**: Utility-first CSS with custom Glassmorphism theme
- **Iconoir**: Beautiful open-source icon library (CSS-based)
- **Blade Templates**: Laravel's powerful templating engine

### Native Application
- **NativePHP 2**: Cross-platform desktop wrapper
- **Electron**: Native application runtime
- System tray/menu bar integration
- Native file dialogs and system notifications

## User Workflows

### First Time Setup
1. Launch PurrAI application
2. Click the kitten icon in menu bar
3. Navigate to Settings (⚙️)
4. Add AI provider API keys or configure local Ollama
5. Select preferred model
6. Start chatting

### Daily Usage
1. **Start Conversation**: Click menu bar icon, type question
2. **Attach Files**: Click attachment icon to upload documents
3. **Paste Images**: Use Ctrl+V (Cmd+V) to paste screenshots
4. **View History**: Browse past conversations from dropdown
5. **New Chat**: Start fresh conversation anytime

### Privacy-Focused Usage (Ollama)
1. Install Ollama locally
2. Pull preferred model: `ollama pull llama2`
3. Configure Ollama endpoint in Settings
4. All AI processing happens locally without API calls

## Development Commands

### Running the Application
```bash
# Development mode with hot reload
php artisan native:serve

# Production build
php artisan native:build
```

### Asset Building
```bash
# Development with watch
npm run dev

# Production build
npm run build
```

### Database
```bash
# Run migrations
php artisan migrate

# Fresh database with seed data
php artisan migrate:fresh --seed
```

## Configuration

### Environment Variables
- `APP_NAME`: Application name (PurrAI)
- `APP_ENV`: Environment (local/production)
- `DB_CONNECTION`: Database driver (sqlite)
- AI provider API keys configured in Settings UI

### Config Files
- `config/purrai.php`: PurrAI-specific configuration
- `config/database.php`: Database configuration (SQLite)
- `config/app.php`: Laravel application configuration

## Key Models & Components

### Livewire Components
- `Chat`: Main chat interface with message handling
- `Settings`: AI provider and application configuration
- `WindowControls`: Native window controls (minimize, close)

### Database Models
- `Conversation`: Stores chat conversations
- `Message`: Individual messages within conversations
- `Attachment`: File attachments linked to messages

### Services
- AI provider integration via PrismPHP
- File upload and processing
- Image analysis and screenshot handling
- Conversation history management

## Design Principles

### User Experience
- **Instant Access**: Always available from menu bar
- **Minimal Friction**: Quick setup, intuitive interface
- **Privacy First**: Local storage, optional local AI models
- **Beautiful UI**: Glassmorphism design with smooth animations

### Technical Excellence
- **Modern Stack**: Latest Laravel, Livewire, Tailwind versions
- **Type Safety**: PHP 8.4+ with strict types
- **Code Quality**: PSR-12, PHPStan Level 5, Pint formatting
- **Testing**: Pest 4 for comprehensive test coverage

### Cross-Platform
- Consistent experience on Linux, Windows, macOS
- Native system integration (menu bar, notifications)
- Responsive to system theme changes
- Minimal resource usage

## Target Audience

- **Developers**: Quick AI assistance while coding
- **Content Creators**: Image analysis and document processing
- **Privacy-Conscious Users**: Local AI models via Ollama
- **Productivity Enthusiasts**: Always-accessible AI companion
- **Multi-Platform Users**: Consistent experience across OSes
