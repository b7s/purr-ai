# Frontend Architecture

PurrAI uses a modern frontend stack combining Livewire, Alpine.js, and Tailwind CSS for a reactive, component-based UI.

## Technology Stack

### Core Technologies

- **Livewire 3** - Server-side reactive components
- **Alpine.js 3** - Lightweight JavaScript framework
- **Tailwind CSS 4** - Utility-first CSS framework
- **Vite 7** - Fast build tool and dev server
- **Iconoir** - Beautiful icon library

### JavaScript Libraries

- **marked** - Markdown parser
- **dompurify** - HTML sanitizer
- **@tailwindcss/typography** - Prose styling

## CSS Architecture

### Tailwind CSS 4

PurrAI uses Tailwind CSS v4 with CSS-first configuration:

```css
/* resources/css/app.css */
@import "tailwindcss";
@plugin "@tailwindcss/typography";

@theme {
    --font-sans: "Inter", "Instrument Sans", ui-sans-serif, system-ui, sans-serif;
    --color-accent: #000000;
    --color-accent-fg: #ffffff;
    --animate-slide-up: slide-up 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
```

### Component-Based CSS

Each major feature has its own CSS file:

```css
/* resources/css/components/chat.css */
@layer components {
    .chat-messages {
        @apply flex-1 overflow-y-auto p-6 md:p-10 space-y-8 pb-44!;
        scroll-behavior: smooth;
    }
    
    .chat-bubble {
        @apply px-5 py-3 text-[15px] leading-relaxed shadow-sm;
    }
}
```

### Dark Mode

Dark mode is handled via Tailwind's `dark:` variant:

```html
<div class="bg-white dark:bg-slate-950">
    <p class="text-slate-900 dark:text-white">Content</p>
</div>
```

Theme switching is managed by Settings component:

```javascript
// Dispatched from Settings
this.dispatch('theme-changed', theme: this.themeMode);

// Handled in app layout
@theme-changed.window="
    if ($event.detail.theme === 'dark') {
        document.documentElement.classList.add('dark');
    } else if ($event.detail.theme === 'light') {
        document.documentElement.classList.remove('dark');
    } else {
        // Automatic - follow system preference
        if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }
"
```

## JavaScript Architecture

### Module Organization

```javascript
// resources/js/app.js
import "./bootstrap";
import "./toast";
import "./audio-devices";
import "./speech-recognition";
import "./chat-stream";
```

### Alpine.js Components

#### Global Components

Registered in `app.js`:

```javascript
document.addEventListener("alpine:init", () => {
    Alpine.data("historyDropdown", (initialConversations, initialHasMorePages, initialSearchQuery) => ({
        open: false,
        conversations: initialConversations,
        hasMorePages: initialHasMorePages,
        searchTerm: initialSearchQuery,
        
        init() {
            // Component initialization
        },
        
        loadConv(id) {
            // Load conversation
        },
        
        // ... more methods
    }));
});
```

#### Inline Components

Defined directly in Blade:

```html
<div x-data="{
    scrollToBottom() {
        let container = document.getElementById('messages-container');
        if (container) {
            container.scrollTo({
                top: container.scrollHeight,
                behavior: 'smooth'
            });
        }
    }
}">
    <!-- Component content -->
</div>
```

### Chat Streaming Module

```javascript
// resources/js/chat-stream.js
import { marked } from "marked";
import DOMPurify from "dompurify";

// Configure marked
marked.setOptions({
    breaks: true,
    gfm: true,
});

async function streamAIResponse(conversationId, selectedModel) {
    const response = await fetch("/api/chat/stream", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": getCSRFToken(),
            Accept: "text/event-stream",
        },
        body: JSON.stringify({
            conversation_id: conversationId,
            selected_model: selectedModel,
        }),
    });
    
    const reader = response.body.getReader();
    const decoder = new TextDecoder();
    
    while (true) {
        const { done, value } = await reader.read();
        if (done) break;
        
        const chunk = decoder.decode(value, { stream: true });
        // Process SSE data
    }
}

// Listen for Livewire events
document.addEventListener("livewire:init", () => {
    Livewire.on("start-ai-stream", (params) => {
        streamAIResponse(params.conversationId, params.selectedModel);
    });
});
```

### Speech Recognition Module

```javascript
// resources/js/speech-recognition.js
class SpeechRecognition {
    constructor() {
        this.isRecording = false;
        this.mediaRecorder = null;
        this.audioChunks = [];
    }
    
    async startRecording() {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        this.mediaRecorder = new MediaRecorder(stream);
        
        this.mediaRecorder.ondataavailable = (event) => {
            this.audioChunks.push(event.data);
        };
        
        this.mediaRecorder.onstop = async () => {
            const audioBlob = new Blob(this.audioChunks, { type: 'audio/webm' });
            await this.transcribe(audioBlob);
        };
        
        this.mediaRecorder.start();
        this.isRecording = true;
    }
    
    stopRecording() {
        if (this.mediaRecorder && this.isRecording) {
            this.mediaRecorder.stop();
            this.isRecording = false;
        }
    }
    
    async transcribe(audioBlob) {
        const formData = new FormData();
        formData.append('audio', audioBlob);
        
        const response = await fetch('/api/transcribe', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': getCSRFToken(),
            },
        });
        
        const data = await response.json();
        return data.text;
    }
}
```

## Livewire Integration

### Component Communication

#### Parent to Child

```blade
<!-- Parent component -->
<livewire:child-component :data="$parentData" />
```

#### Child to Parent

```php
// Child component
$this->dispatch('event-name', data: $value);
```

```blade
<!-- Parent component -->
<div @event-name.window="handleEvent($event.detail)">
```

#### Component to JavaScript

```php
// Livewire component
$this->dispatch('js-event', data: $value);
```

```javascript
// JavaScript
document.addEventListener('livewire:init', () => {
    Livewire.on('js-event', (params) => {
        console.log(params.data);
    });
});
```

### Wire Directives

```blade
<!-- Two-way binding -->
<input wire:model="message" />

<!-- Lazy binding (on blur) -->
<input wire:model.blur="userName" />

<!-- Live binding (real-time) -->
<input wire:model.live="searchQuery" />

<!-- Method calls -->
<button wire:click="sendMessage">Send</button>

<!-- Form submission -->
<form wire:submit="save">
    <!-- Form fields -->
</form>

<!-- Loading states -->
<div wire:loading>Saving...</div>

<!-- Ignore wire updates -->
<div wire:ignore>
    <!-- Third-party components -->
</div>
```

## Build Process

### Development

```bash
# Start dev server with hot reload
npm run dev

# Or use composer script
composer run dev
```

### Production

```bash
# Build optimized assets
npm run build
```

### Vite Configuration

```javascript
// vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
```

## Asset Management

### Including Assets in Blade

```blade
<!DOCTYPE html>
<html>
<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <!-- Content -->
</body>
</html>
```

### Public Assets

Static assets in `public/` directory:

```blade
<img src="{{ asset('images/logo.png') }}" alt="Logo">
```

## Performance Optimization

### CSS

- Component-based organization
- Tailwind purging in production
- Critical CSS inlined
- Lazy loading for non-critical styles

### JavaScript

- Code splitting with Vite
- Lazy loading modules
- Event delegation
- Debounced inputs

### Livewire

- Wire:ignore for static content
- Lazy loading components
- Polling only when needed
- Efficient property updates

## Browser Support

- Chrome/Edge 90+
- Firefox 88+
- Safari 14+

## Accessibility

- Semantic HTML
- ARIA labels
- Keyboard navigation
- Focus management
- Screen reader support

## Testing

### Browser Tests (Pest v4)

```php
it('displays chat interface', function () {
    $page = visit('/');
    
    $page->assertSee('Welcome to PurrAI')
        ->assertNoJavascriptErrors();
});
```

### JavaScript Tests

```bash
# Run JS tests (if configured)
npm test
```

## Debugging

### Browser Console

```javascript
// Enable Livewire debugging
window.Livewire.devTools(true);

// Log Alpine data
Alpine.store('debug', true);
```

### Vue DevTools

Livewire 3 is compatible with Vue DevTools for component inspection.
