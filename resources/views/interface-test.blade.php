<!DOCTYPE html>
<html lang="pt-br" class="antialiased">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sequoia Chat UI - Fixed Layout</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/iconoir-icons/iconoir@main/css/iconoir.css">

    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>

    <style type="text/tailwindcss">
        @theme {
            --font-sans: "Inter", sans-serif;
            --color-accent: #000000;
            --color-accent-fg: #ffffff;
            
            --animate-slide-up: slide-up 0.4s cubic-bezier(0.16, 1, 0.3, 1);

            @keyframes slide-up {
                0% { opacity: 0; transform: translateY(20px); }
                100% { opacity: 1; transform: translateY(0); }
            }
        }

        /* Reset e Base */
        body {
            @apply font-sans text-neutral-900 dark:text-neutral-100 h-screen w-screen overflow-hidden transition-colors duration-500;
            background-image: url('https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?q=80&w=2564&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
        }

        .dark body {
            background-image: url('https://images.unsplash.com/photo-1506318137071-a8bcbf6d9421?q=80&w=2564&auto=format&fit=crop');
        }

        @layer components {
            
            /* Painel Principal */
            .glass-panel {
                @apply h-full w-full flex flex-col relative overflow-hidden;
                @apply backdrop-blur-3xl shadow-2xl transition-all duration-500;
                @apply border border-white/40 dark:border-white/10;
                @apply bg-white/85 dark:bg-[#121212]/75;
            }

            .glass-header {
                @apply h-16 px-6 flex items-center justify-between shrink-0 z-20;
                @apply border-b border-black/5 dark:border-white/5;
                @apply bg-white/40 dark:bg-white/5 backdrop-blur-md;
            }

            /* Chat Rows */
            .chat-row {
                @apply flex gap-4 max-w-3xl w-full animate-slide-up;
            }
            .chat-row.user { @apply ml-auto justify-end; }

            .chat-bubble {
                @apply px-5 py-3 text-[15px] leading-relaxed shadow-sm relative max-w-full break-words;
            }
            .chat-bubble.primary {
                @apply bg-accent text-accent-fg rounded-[20px] rounded-tr-[4px] dark:bg-white dark:text-black;
            }
            .chat-bubble.secondary {
                @apply bg-white/60 dark:bg-white/10 backdrop-blur-md border border-white/50 dark:border-white/5;
                @apply text-neutral-800 dark:text-neutral-200 rounded-[20px] rounded-tl-[4px];
            }

            /* Arquivos */
            .file-card {
                @apply flex items-center gap-3 p-2 pr-4 rounded-xl cursor-pointer transition-all;
                @apply bg-white/50 dark:bg-white/5 border border-white/40 dark:border-white/10;
                @apply hover:bg-white/80 dark:hover:bg-white/15;
            }
            .file-icon {
                @apply w-10 h-10 rounded-lg flex items-center justify-center text-lg;
                @apply bg-neutral-100 dark:bg-white/10 text-neutral-600 dark:text-neutral-300;
            }

            /* --- CORREÇÃO DO INPUT --- */
            
            /* Container Flutuante (Pílula) */
            .input-dock {
                @apply absolute bottom-6 left-1/2 -translate-x-1/2 w-[calc(100%-3rem)] max-w-3xl z-30;
                @apply bg-white/70 dark:bg-[#1e1e1e]/80 backdrop-blur-2xl;
                @apply border border-white/50 dark:border-white/10;
                /* Arredondamento completo estilo pílula */
                @apply rounded-[32px] shadow-lg ring-1 ring-black/5 dark:ring-white/5;
                /* Layout Flex alinhado na base (bottom) */
                @apply flex items-end gap-2 p-2 transition-shadow focus-within:shadow-xl;
            }

            /* Botões Circulares Perfeitos */
            .circle-btn {
                @apply w-10 h-10 rounded-full flex-shrink-0 flex items-center justify-center cursor-pointer transition-transform active:scale-95;
            }
            
            /* Botões Secundários (Cinza/Transparente) */
            .circle-btn.ghost {
                @apply text-neutral-500 hover:text-neutral-900 dark:hover:text-white hover:bg-black/5 dark:hover:bg-white/10;
            }

            /* Botão Primário (Preto/Branco) */
            .circle-btn.primary {
                @apply bg-accent text-accent-fg dark:bg-white dark:text-black shadow-md hover:scale-105;
            }

            /* Textarea Ajustado */
            .input-field {
                @apply w-full bg-transparent text-[15px] text-neutral-900 dark:text-white;
                @apply placeholder-neutral-500 focus:outline-none resize-none max-h-32;
                /* Padding vertical calibrado para alinhar texto com os botões de 40px */
                @apply py-2.5 px-2; 
            }
        }
        
        .scrollbar-hide::-webkit-scrollbar { display: none; }
    </style>
</head>

<body class="flex items-center justify-center p-4 md:p-6">

    <main class="glass-panel rounded-[2.5rem]">

        <header class="glass-header">
            <div class="flex items-center gap-3">
                <div class="w-3 h-3 rounded-full bg-[#FF5F57] border border-black/10 shadow-sm"></div>
                <div class="w-3 h-3 rounded-full bg-[#FEBC2E] border border-black/10 shadow-sm"></div>
                <div class="w-3 h-3 rounded-full bg-[#28C840] border border-black/10 shadow-sm"></div>

                <div class="ml-4 flex items-center gap-2 opacity-80">
                    <i class="iconoir-sparkles text-lg"></i>
                    <span class="text-sm font-medium tracking-wide">Sequoia AI</span>
                </div>
            </div>

            <div class="flex gap-1">
                <button class="circle-btn ghost w-9 h-9" onclick="toggleTheme()">
                    <i class="iconoir-half-moon text-lg block dark:hidden"></i>
                    <i class="iconoir-sun-light text-lg hidden dark:block"></i>
                </button>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-6 md:p-10 space-y-8 pb-36 scrollbar-hide" id="scroll-container">

            <div class="chat-row">
                <div
                    class="purr-ai-logo w-8 h-8 rounded-full bg-gradient-to-tr from-neutral-200 to-white dark:from-neutral-700 dark:to-neutral-600 flex items-center justify-center shadow-inner shrink-0">
                    <i class="iconoir-sparkles text-sm text-neutral-600 dark:text-neutral-300"></i>
                </div>
                <div class="space-y-2">
                    <div class="chat-bubble secondary">
                        Olá! Estou pronto para ajudar no design. O que vamos criar hoje?
                    </div>
                </div>
            </div>

            <div class="chat-row user flex-col items-end">
                <div class="chat-bubble primary">
                    Aqui estão as referências e os dados do projeto.
                </div>

                <div class="flex flex-wrap justify-end gap-2 mt-1 max-w-md">
                    <div class="file-card group">
                        <div class="file-icon text-orange-500 dark:text-orange-400 bg-orange-50 dark:bg-orange-900/20">
                            <i class="iconoir-page text-xl"></i>
                        </div>
                        <div class="text-left">
                            <p class="text-xs font-semibold">Req_v3.pdf</p>
                            <p class="text-[10px] opacity-60">2.4 MB</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-1.5 mt-1 w-full max-w-[320px]">
                    <div
                        class="aspect-square rounded-2xl overflow-hidden border border-white/20 relative group cursor-pointer">
                        <img src="https://images.unsplash.com/photo-1516131206008-dd041a9767fd?q=80&w=500&auto=format&fit=crop"
                            class="w-full h-full object-cover transition-transform group-hover:scale-110 duration-500">
                    </div>
                    <div
                        class="aspect-square rounded-2xl overflow-hidden border border-white/20 relative group cursor-pointer">
                        <img src="https://images.unsplash.com/photo-1512951670161-b5c6c6391c47?q=80&w=500&auto=format&fit=crop"
                            class="w-full h-full object-cover transition-transform group-hover:scale-110 duration-500">
                    </div>
                    <div
                        class="aspect-square rounded-2xl overflow-hidden border border-white/20 relative group cursor-pointer">
                        <div
                            class="absolute inset-0 bg-black/50 backdrop-blur-xs z-10 flex items-center justify-center text-white font-medium">
                            +3</div>
                        <img src="https://images.unsplash.com/photo-1518005052351-03fb43005ca9?q=80&w=500&auto=format&fit=crop"
                            class="w-full h-full object-cover">
                    </div>
                </div>
                <div class="text-[10px] opacity-40 pr-1 font-medium">10:42</div>
            </div>

            <div class="chat-row">
                <div
                    class="w-8 h-8 rounded-full bg-gradient-to-tr from-neutral-200 to-white dark:from-neutral-700 dark:to-neutral-600 flex items-center justify-center shadow-inner shrink-0">
                    <i class="iconoir-sparkles text-sm text-neutral-600 dark:text-neutral-300"></i>
                </div>
                <div class="space-y-3 w-full max-w-2xl">
                    <div class="chat-bubble secondary">
                        <p>Recebido. A paleta de cores está alinhada.</p>
                    </div>
                </div>
            </div>

        </div>

        <div class="input-dock">
            <button class="circle-btn ghost" title="Anexar Arquivo">
                <i class="iconoir-plus text-xl"></i>
            </button>

            <textarea class="input-field" rows="1" placeholder="Digite sua mensagem..."></textarea>

            <div class="flex gap-1 pb-0">
                <button class="circle-btn ghost" title="Gravar Áudio">
                    <i class="iconoir-microphone text-xl"></i>
                </button>
                <button class="circle-btn primary" title="Enviar">
                    <i class="iconoir-arrow-up text-xl font-bold stroke-[3px]"></i>
                </button>
            </div>
        </div>

    </main>

    <script>
        function toggleTheme() {
            const html = document.documentElement;
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                html.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
        }
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
        const textarea = document.querySelector('textarea');
        textarea.addEventListener('input', function () {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    </script>
</body>

</html>