 <?php $__env->slot('headerActions', null, []); ?> 
    <a
        href="<?php echo e(getPreviousChatUrl()); ?>"
        wire:navigate
        class="circle-btn ghost"
    >
        <i class="iconoir-arrow-left text-xl"></i>
    </a>
 <?php $__env->endSlot(); ?>

<?php
    $tabs = [
        'chat' => ['label' => __('settings.tabs.chat')],
        'ai_providers' => ['label' => __('settings.tabs.ai_providers'), 'icon' => 'sparks'],
        'other' => ['label' => __('settings.tabs.other')],
    ];

    $responseDetailOptions = [
        'detailed' => ['label' => __('settings.chat.response_detail_detailed')],
        'short' => ['label' => __('settings.chat.response_detail_short')],
    ];

    $responseToneOptions = collect(config('purrai.response_tones'))
        ->mapWithKeys(function ($tone) {
            return [
                $tone['value'] => [
                    'icon' => $tone['icon'],
                    'label' => __($tone['label']),
                    'description' => __($tone['description']),
                    'height' => 'h-24',
                    'class' => 'flex flex-col items-center justify-center gap-1.5 h-full px-2',
                    'labelClass' => 'text-sm font-medium',
                ],
            ];
        })
        ->toArray();

    $themeModeOptions = [
        'light' => [
            'icon' => 'sun-light',
            'iconClass' => 'text-lg mr-1.5',
            'label' => __('settings.other.theme_light'),
            'class' => 'flex items-center justify-center',
        ],
        'dark' => [
            'icon' => 'half-moon',
            'iconClass' => 'text-lg mr-1.5',
            'label' => __('settings.other.theme_dark'),
            'class' => 'flex items-center justify-center',
        ],
        'automatic' => [
            'icon' => 'settings',
            'iconClass' => 'text-lg mr-1.5',
            'label' => __('settings.other.theme_automatic'),
            'class' => 'flex items-center justify-center',
        ],
    ];
?>

<div
    class="h-full flex flex-col overflow-y-auto"
    @keydown.escape.window="window.location.href = '<?php echo e(getPreviousChatUrl()); ?>'"
    tabindex="-1"
>
    <div class="w-full max-w-4xl mx-auto px-6 md:px-10 py-6 md:py-10 pb-24 space-y-8">
        
        <div class="space-y-2">
            <h1 class="settings-title">
                <?php echo e(__('settings.title')); ?>

            </h1>
            <p class="settings-description">
                <?php echo e(__('settings.auto_save_notice')); ?>

            </p>
        </div>

        
        <?php if (isset($component)) { $__componentOriginal74888cb3b248a08ce228c04e2cfe93a9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal74888cb3b248a08ce228c04e2cfe93a9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.tabs','data' => ['tabs' => $tabs,'active' => request()->input('tab')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.tabs'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['tabs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tabs),'active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->input('tab'))]); ?>
            
            <?php if (isset($component)) { $__componentOriginal1bed0309c69d88a1723304e94fad3fa5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1bed0309c69d88a1723304e94fad3fa5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.tab-content','data' => ['name' => 'chat']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.tab-content'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chat']); ?>
                <div class="card">
                    <?php if (isset($component)) { $__componentOriginal65bd7e7dbd93cec773ad6501ce127e46 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal65bd7e7dbd93cec773ad6501ce127e46 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.input','data' => ['label' => __('settings.chat.mascot_name'),'model' => 'mascotName','placeholder' => __('settings.chat.mascot_name_placeholder')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.chat.mascot_name')),'model' => 'mascotName','placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.chat.mascot_name_placeholder'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal65bd7e7dbd93cec773ad6501ce127e46)): ?>
<?php $attributes = $__attributesOriginal65bd7e7dbd93cec773ad6501ce127e46; ?>
<?php unset($__attributesOriginal65bd7e7dbd93cec773ad6501ce127e46); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal65bd7e7dbd93cec773ad6501ce127e46)): ?>
<?php $component = $__componentOriginal65bd7e7dbd93cec773ad6501ce127e46; ?>
<?php unset($__componentOriginal65bd7e7dbd93cec773ad6501ce127e46); ?>
<?php endif; ?>
                </div>

                <div class="card space-y-6">
                    <?php if (isset($component)) { $__componentOriginal2d35919db4362f189511ef57def4e650 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2d35919db4362f189511ef57def4e650 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.radio-group','data' => ['label' => __('settings.chat.response_detail'),'options' => $responseDetailOptions,'model' => 'responseDetail']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.radio-group'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.chat.response_detail')),'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($responseDetailOptions),'model' => 'responseDetail']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2d35919db4362f189511ef57def4e650)): ?>
<?php $attributes = $__attributesOriginal2d35919db4362f189511ef57def4e650; ?>
<?php unset($__attributesOriginal2d35919db4362f189511ef57def4e650); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2d35919db4362f189511ef57def4e650)): ?>
<?php $component = $__componentOriginal2d35919db4362f189511ef57def4e650; ?>
<?php unset($__componentOriginal2d35919db4362f189511ef57def4e650); ?>
<?php endif; ?>

                    <?php if (isset($component)) { $__componentOriginal2d35919db4362f189511ef57def4e650 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2d35919db4362f189511ef57def4e650 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.radio-group','data' => ['label' => __('settings.chat.response_tone'),'options' => $responseToneOptions,'model' => 'responseTone','columns' => '2 sm:grid-cols-3 md:grid-cols-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.radio-group'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.chat.response_tone')),'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($responseToneOptions),'model' => 'responseTone','columns' => '2 sm:grid-cols-3 md:grid-cols-4']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2d35919db4362f189511ef57def4e650)): ?>
<?php $attributes = $__attributesOriginal2d35919db4362f189511ef57def4e650; ?>
<?php unset($__attributesOriginal2d35919db4362f189511ef57def4e650); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2d35919db4362f189511ef57def4e650)): ?>
<?php $component = $__componentOriginal2d35919db4362f189511ef57def4e650; ?>
<?php unset($__componentOriginal2d35919db4362f189511ef57def4e650); ?>
<?php endif; ?>
                </div>

                <div class="card">
                    <?php if (isset($component)) { $__componentOriginalb5e8d8ae92d68ac68a76e18b82b039ee = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb5e8d8ae92d68ac68a76e18b82b039ee = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.toggle','data' => ['label' => new \Illuminate\Support\HtmlString('<img src=\'' . asset('images/mascot/logo.svg') . '\' alt=\'\' class=\'w-8 inline-block me-2\'>' . __('settings.chat.respond_as_cat')),'model' => 'respondAsACat','checked' => $respondAsACat]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.toggle'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(new \Illuminate\Support\HtmlString('<img src=\'' . asset('images/mascot/logo.svg') . '\' alt=\'\' class=\'w-8 inline-block me-2\'>' . __('settings.chat.respond_as_cat'))),'model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('respondAsACat'),'checked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($respondAsACat)]); ?>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb5e8d8ae92d68ac68a76e18b82b039ee)): ?>
<?php $attributes = $__attributesOriginalb5e8d8ae92d68ac68a76e18b82b039ee; ?>
<?php unset($__attributesOriginalb5e8d8ae92d68ac68a76e18b82b039ee); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb5e8d8ae92d68ac68a76e18b82b039ee)): ?>
<?php $component = $__componentOriginalb5e8d8ae92d68ac68a76e18b82b039ee; ?>
<?php unset($__componentOriginalb5e8d8ae92d68ac68a76e18b82b039ee); ?>
<?php endif; ?>
                </div>

                
                <div
                    class="card space-y-4"
                    id="active-speech-recognition-setting"
                >
                    <label class="settings-label">
                        <?php echo e(__('settings.other.speech_recognition')); ?>

                    </label>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(\App\Services\WhisperService::hasPendingConfiguration()): ?>
                        <div class="whisper-alert">
                            <div class="whisper-alert-content">
                                <i class="iconoir-warning-triangle whisper-alert-icon"></i>
                                <div class="whisper-alert-body">
                                    <h3 class="whisper-alert-title">
                                        <?php echo e(__('settings.other.speech_recognition_setup')); ?>

                                    </h3>

                                    
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($useLocalSpeech): ?>
                                        <?php if (isset($component)) { $__componentOriginal62f0cbd196dcf740ecc58a36b72a2245 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal62f0cbd196dcf740ecc58a36b72a2245 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.settings.whisper-status','data' => ['status' => $whisperStatus,'isDownloading' => $isDownloadingWhisper,'progress' => $downloadProgress,'error' => $downloadError]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('settings.whisper-status'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($whisperStatus),'isDownloading' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($isDownloadingWhisper),'progress' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($downloadProgress),'error' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($downloadError)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal62f0cbd196dcf740ecc58a36b72a2245)): ?>
<?php $attributes = $__attributesOriginal62f0cbd196dcf740ecc58a36b72a2245; ?>
<?php unset($__attributesOriginal62f0cbd196dcf740ecc58a36b72a2245); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal62f0cbd196dcf740ecc58a36b72a2245)): ?>
<?php $component = $__componentOriginal62f0cbd196dcf740ecc58a36b72a2245; ?>
<?php unset($__componentOriginal62f0cbd196dcf740ecc58a36b72a2245); ?>
<?php endif; ?>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if (isset($component)) { $__componentOriginalb5e8d8ae92d68ac68a76e18b82b039ee = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb5e8d8ae92d68ac68a76e18b82b039ee = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.toggle','data' => ['label' => __('settings.speech.enable'),'description' => __('settings.speech.enable_description'),'model' => 'speechToTextEnabled','checked' => $speechToTextEnabled]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.toggle'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.speech.enable')),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.speech.enable_description')),'model' => 'speechToTextEnabled','checked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($speechToTextEnabled)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb5e8d8ae92d68ac68a76e18b82b039ee)): ?>
<?php $attributes = $__attributesOriginalb5e8d8ae92d68ac68a76e18b82b039ee; ?>
<?php unset($__attributesOriginalb5e8d8ae92d68ac68a76e18b82b039ee); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb5e8d8ae92d68ac68a76e18b82b039ee)): ?>
<?php $component = $__componentOriginalb5e8d8ae92d68ac68a76e18b82b039ee; ?>
<?php unset($__componentOriginalb5e8d8ae92d68ac68a76e18b82b039ee); ?>
<?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($speechToTextEnabled): ?>
                        <?php if (isset($component)) { $__componentOriginalb5e8d8ae92d68ac68a76e18b82b039ee = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb5e8d8ae92d68ac68a76e18b82b039ee = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.toggle','data' => ['label' => __('settings.speech.use_local'),'description' => new \Illuminate\Support\HtmlString(view('components.ui.badge', ['slot' => __('settings.speech.private')])->render() . '&nbsp;&nbsp;' . __('settings.speech.use_local_description')),'model' => 'useLocalSpeech','checked' => $useLocalSpeech]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.toggle'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.speech.use_local')),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(new \Illuminate\Support\HtmlString(view('components.ui.badge', ['slot' => __('settings.speech.private')])->render() . '&nbsp;&nbsp;' . __('settings.speech.use_local_description'))),'model' => 'useLocalSpeech','checked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($useLocalSpeech)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb5e8d8ae92d68ac68a76e18b82b039ee)): ?>
<?php $attributes = $__attributesOriginalb5e8d8ae92d68ac68a76e18b82b039ee; ?>
<?php unset($__attributesOriginalb5e8d8ae92d68ac68a76e18b82b039ee); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb5e8d8ae92d68ac68a76e18b82b039ee)): ?>
<?php $component = $__componentOriginalb5e8d8ae92d68ac68a76e18b82b039ee; ?>
<?php unset($__componentOriginalb5e8d8ae92d68ac68a76e18b82b039ee); ?>
<?php endif; ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$useLocalSpeech): ?>
                            <?php if (isset($component)) { $__componentOriginal231e2c645bf8af0c5c05a5dc5a94c862 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal231e2c645bf8af0c5c05a5dc5a94c862 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.select','data' => ['label' => __('settings.speech.provider') . ' *','description' => __('settings.speech.provider_description'),'placeholder' => __('settings.speech.provider_placeholder'),'model' => 'speechProvider','options' => $this->getSpeechProviderOptions()]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.speech.provider') . ' *'),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.speech.provider_description')),'placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.speech.provider_placeholder')),'model' => 'speechProvider','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($this->getSpeechProviderOptions())]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal231e2c645bf8af0c5c05a5dc5a94c862)): ?>
<?php $attributes = $__attributesOriginal231e2c645bf8af0c5c05a5dc5a94c862; ?>
<?php unset($__attributesOriginal231e2c645bf8af0c5c05a5dc5a94c862); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal231e2c645bf8af0c5c05a5dc5a94c862)): ?>
<?php $component = $__componentOriginal231e2c645bf8af0c5c05a5dc5a94c862; ?>
<?php unset($__componentOriginal231e2c645bf8af0c5c05a5dc5a94c862); ?>
<?php endif; ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <?php if (isset($component)) { $__componentOriginalb5e8d8ae92d68ac68a76e18b82b039ee = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb5e8d8ae92d68ac68a76e18b82b039ee = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.toggle','data' => ['label' => __('settings.speech.auto_send'),'description' => __('settings.speech.auto_send_description'),'model' => 'autoSendAfterTranscription','checked' => $autoSendAfterTranscription ?? false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.toggle'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.speech.auto_send')),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.speech.auto_send_description')),'model' => 'autoSendAfterTranscription','checked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($autoSendAfterTranscription ?? false)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb5e8d8ae92d68ac68a76e18b82b039ee)): ?>
<?php $attributes = $__attributesOriginalb5e8d8ae92d68ac68a76e18b82b039ee; ?>
<?php unset($__attributesOriginalb5e8d8ae92d68ac68a76e18b82b039ee); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb5e8d8ae92d68ac68a76e18b82b039ee)): ?>
<?php $component = $__componentOriginalb5e8d8ae92d68ac68a76e18b82b039ee; ?>
<?php unset($__componentOriginalb5e8d8ae92d68ac68a76e18b82b039ee); ?>
<?php endif; ?>

                        
                        <div
                            class="audio_device-settings"
                            x-data="audio_deviceSelector"
                        >
                            <label class="settings-label">
                                <?php echo e(__('settings.speech.audio_device_settings')); ?>

                            </label>

                            <div class="space-y-4">
                                
                                <div class="flex items-center gap-2">
                                    <div class="flex-1">
                                        <label class="settings-label text-sm">
                                            <?php echo e(__('settings.speech.select_audio_device')); ?>

                                        </label>
                                        <select
                                            x-model="selectedDeviceId"
                                            @change="selectDevice($event.target.value)"
                                            class="settings-input"
                                            :disabled="loading"
                                        >
                                            <template x-if="loading">
                                                <option><?php echo e(__('settings.speech.loading_devices')); ?></option>
                                            </template>
                                            <template x-if="!loading && devices.length === 0">
                                                <option value="default"><?php echo e(__('settings.speech.default_audio_device')); ?>

                                                </option>
                                            </template>
                                            <template
                                                x-for="device in devices"
                                                :key="device.id"
                                            >
                                                <option
                                                    :value="device.id"
                                                    x-text="device.label"
                                                ></option>
                                            </template>
                                        </select>
                                        <p class="help-text">
                                            <?php echo e(__('settings.speech.select_audio_device_description')); ?>

                                        </p>
                                    </div>

                                    <button
                                        type="button"
                                        @click="refreshDevices()"
                                        class="w-10 h-10 rounded-lg flex items-center justify-center text-slate-600 hover:bg-slate-100 transition-colors dark:text-slate-400 dark:hover:bg-slate-700 mb-1"
                                        :disabled="loading"
                                        title="<?php echo e(__('settings.speech.refresh_devices')); ?>"
                                    >
                                        <i
                                            class="iconoir-refresh text-lg"
                                            :class="{ 'animate-spin': loading }"
                                        ></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        
                        <?php if (isset($component)) { $__componentOriginal2d35919db4362f189511ef57def4e650 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2d35919db4362f189511ef57def4e650 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.radio-group','data' => ['label' => __('settings.speech.noise_suppression'),'description' => __('settings.speech.noise_suppression_description'),'model' => 'noiseSuppressionLevel','columns' => '2 sm:grid-cols-3 md:grid-cols-4','options' => [
                                'disabled' => [
                                    'label' => __('settings.speech.noise_level_disabled'),
                                    'description' => __('settings.speech.noise_level_disabled_desc'),
                                    'icon' => 'sound-off',
                                ],
                                'light' => [
                                    'label' => __('settings.speech.noise_level_light'),
                                    'description' => __('settings.speech.noise_level_light_desc'),
                                    'icon' => 'sound-low',
                                ],
                                'medium' => [
                                    'label' => __('settings.speech.noise_level_medium'),
                                    'description' => __('settings.speech.noise_level_medium_desc'),
                                    'icon' => 'sound-min',
                                ],
                                'high' => [
                                    'label' => __('settings.speech.noise_level_high'),
                                    'description' => __('settings.speech.noise_level_high_desc'),
                                    'icon' => 'sound-high',
                                ],
                            ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.radio-group'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.speech.noise_suppression')),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.speech.noise_suppression_description')),'model' => 'noiseSuppressionLevel','columns' => '2 sm:grid-cols-3 md:grid-cols-4','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
                                'disabled' => [
                                    'label' => __('settings.speech.noise_level_disabled'),
                                    'description' => __('settings.speech.noise_level_disabled_desc'),
                                    'icon' => 'sound-off',
                                ],
                                'light' => [
                                    'label' => __('settings.speech.noise_level_light'),
                                    'description' => __('settings.speech.noise_level_light_desc'),
                                    'icon' => 'sound-low',
                                ],
                                'medium' => [
                                    'label' => __('settings.speech.noise_level_medium'),
                                    'description' => __('settings.speech.noise_level_medium_desc'),
                                    'icon' => 'sound-min',
                                ],
                                'high' => [
                                    'label' => __('settings.speech.noise_level_high'),
                                    'description' => __('settings.speech.noise_level_high_desc'),
                                    'icon' => 'sound-high',
                                ],
                            ])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2d35919db4362f189511ef57def4e650)): ?>
<?php $attributes = $__attributesOriginal2d35919db4362f189511ef57def4e650; ?>
<?php unset($__attributesOriginal2d35919db4362f189511ef57def4e650); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2d35919db4362f189511ef57def4e650)): ?>
<?php $component = $__componentOriginal2d35919db4362f189511ef57def4e650; ?>
<?php unset($__componentOriginal2d35919db4362f189511ef57def4e650); ?>
<?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="card">
                    <label class="settings-label">
                        <?php echo e(__('settings.chat.user_description')); ?>

                    </label>
                    <?php if (isset($component)) { $__componentOriginal65bd7e7dbd93cec773ad6501ce127e46 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal65bd7e7dbd93cec773ad6501ce127e46 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.input','data' => ['type' => 'text','wire:model.blur' => 'userName','placeholder' => ''.e(__('settings.chat.user_name_placeholder')).'','class' => 'settings-input']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'text','wire:model.blur' => 'userName','placeholder' => ''.e(__('settings.chat.user_name_placeholder')).'','class' => 'settings-input']); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal65bd7e7dbd93cec773ad6501ce127e46)): ?>
<?php $attributes = $__attributesOriginal65bd7e7dbd93cec773ad6501ce127e46; ?>
<?php unset($__attributesOriginal65bd7e7dbd93cec773ad6501ce127e46); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal65bd7e7dbd93cec773ad6501ce127e46)): ?>
<?php $component = $__componentOriginal65bd7e7dbd93cec773ad6501ce127e46; ?>
<?php unset($__componentOriginal65bd7e7dbd93cec773ad6501ce127e46); ?>
<?php endif; ?>

                    <?php if (isset($component)) { $__componentOriginal62d1193389a71cd99ff302a00abbf991 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal62d1193389a71cd99ff302a00abbf991 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.textarea','data' => ['wire:model.blur' => 'userDescription','placeholder' => ''.e(__('settings.chat.user_description_placeholder')).'','rows' => '3','class' => 'settings-input resize-none mt-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.textarea'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.blur' => 'userDescription','placeholder' => ''.e(__('settings.chat.user_description_placeholder')).'','rows' => '3','class' => 'settings-input resize-none mt-4']); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal62d1193389a71cd99ff302a00abbf991)): ?>
<?php $attributes = $__attributesOriginal62d1193389a71cd99ff302a00abbf991; ?>
<?php unset($__attributesOriginal62d1193389a71cd99ff302a00abbf991); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal62d1193389a71cd99ff302a00abbf991)): ?>
<?php $component = $__componentOriginal62d1193389a71cd99ff302a00abbf991; ?>
<?php unset($__componentOriginal62d1193389a71cd99ff302a00abbf991); ?>
<?php endif; ?>
                </div>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1bed0309c69d88a1723304e94fad3fa5)): ?>
<?php $attributes = $__attributesOriginal1bed0309c69d88a1723304e94fad3fa5; ?>
<?php unset($__attributesOriginal1bed0309c69d88a1723304e94fad3fa5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1bed0309c69d88a1723304e94fad3fa5)): ?>
<?php $component = $__componentOriginal1bed0309c69d88a1723304e94fad3fa5; ?>
<?php unset($__componentOriginal1bed0309c69d88a1723304e94fad3fa5); ?>
<?php endif; ?>

            
            <?php if (isset($component)) { $__componentOriginal1bed0309c69d88a1723304e94fad3fa5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1bed0309c69d88a1723304e94fad3fa5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.tab-content','data' => ['name' => 'ai_providers']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.tab-content'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'ai_providers']); ?>
                <p class="settings-description">
                    <?php echo e(__('settings.ai_providers.description')); ?>

                </p>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = config('purrai.ai_providers', []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $provider): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="card">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $provider['fields']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($field['name'] === 'key' || $field['name'] === 'url'): ?>
                                <div class="flex items-end gap-2 <?php if($index > 0): ?> mt-4 <?php endif; ?>">
                                    <div class="flex-1">
                                        <label class="settings-label">
                                            <?php echo e(__($field['label'])); ?>

                                        </label>
                                        <?php if (isset($component)) { $__componentOriginal65bd7e7dbd93cec773ad6501ce127e46 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal65bd7e7dbd93cec773ad6501ce127e46 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.input','data' => ['type' => ''.e($field['type']).'','wire:model.blur' => 'providers.'.e($provider['key']).'.'.e($field['name']).'','placeholder' => ''.e(__($field['placeholder'])).'','class' => 'settings-input font-mono text-sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => ''.e($field['type']).'','wire:model.blur' => 'providers.'.e($provider['key']).'.'.e($field['name']).'','placeholder' => ''.e(__($field['placeholder'])).'','class' => 'settings-input font-mono text-sm']); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal65bd7e7dbd93cec773ad6501ce127e46)): ?>
<?php $attributes = $__attributesOriginal65bd7e7dbd93cec773ad6501ce127e46; ?>
<?php unset($__attributesOriginal65bd7e7dbd93cec773ad6501ce127e46); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal65bd7e7dbd93cec773ad6501ce127e46)): ?>
<?php $component = $__componentOriginal65bd7e7dbd93cec773ad6501ce127e46; ?>
<?php unset($__componentOriginal65bd7e7dbd93cec773ad6501ce127e46); ?>
<?php endif; ?>
                                    </div>
                                    <button
                                        type="button"
                                        wire:click="fetchModels('<?php echo e($provider['key']); ?>')"
                                        wire:loading.attr="disabled"
                                        wire:target="fetchModels('<?php echo e($provider['key']); ?>')"
                                        class="settings-button"
                                        title="<?php echo e(__('settings.ai_providers.fetch_models')); ?>"
                                    >
                                        <span
                                            wire:loading.remove
                                            wire:target="fetchModels('<?php echo e($provider['key']); ?>')"
                                        >
                                            <i class="iconoir-refresh text-base"></i>
                                        </span>
                                        <span
                                            wire:loading
                                            wire:target="fetchModels('<?php echo e($provider['key']); ?>')"
                                        >
                                            <i class="iconoir-refresh text-base animate-spin"></i>
                                        </span>
                                    </button>
                                </div>
                            <?php else: ?>
                                <label class="settings-label <?php if($index > 0): ?> mt-4 <?php endif; ?>">
                                    <?php echo e(__($field['label'])); ?>

                                </label>
                                <?php if (isset($component)) { $__componentOriginal65bd7e7dbd93cec773ad6501ce127e46 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal65bd7e7dbd93cec773ad6501ce127e46 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.input','data' => ['type' => ''.e($field['type']).'','wire:model.blur' => 'providers.'.e($provider['key']).'.'.e($field['name']).'','placeholder' => ''.e(__($field['placeholder'])).'','class' => 'settings-input font-mono text-sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => ''.e($field['type']).'','wire:model.blur' => 'providers.'.e($provider['key']).'.'.e($field['name']).'','placeholder' => ''.e(__($field['placeholder'])).'','class' => 'settings-input font-mono text-sm']); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal65bd7e7dbd93cec773ad6501ce127e46)): ?>
<?php $attributes = $__attributesOriginal65bd7e7dbd93cec773ad6501ce127e46; ?>
<?php unset($__attributesOriginal65bd7e7dbd93cec773ad6501ce127e46); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal65bd7e7dbd93cec773ad6501ce127e46)): ?>
<?php $component = $__componentOriginal65bd7e7dbd93cec773ad6501ce127e46; ?>
<?php unset($__componentOriginal65bd7e7dbd93cec773ad6501ce127e46); ?>
<?php endif; ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($field['helper'])): ?>
                                    <p class="help-text"><?php echo e(__($field['helper'])); ?></p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1bed0309c69d88a1723304e94fad3fa5)): ?>
<?php $attributes = $__attributesOriginal1bed0309c69d88a1723304e94fad3fa5; ?>
<?php unset($__attributesOriginal1bed0309c69d88a1723304e94fad3fa5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1bed0309c69d88a1723304e94fad3fa5)): ?>
<?php $component = $__componentOriginal1bed0309c69d88a1723304e94fad3fa5; ?>
<?php unset($__componentOriginal1bed0309c69d88a1723304e94fad3fa5); ?>
<?php endif; ?>

            
            <?php if (isset($component)) { $__componentOriginal1bed0309c69d88a1723304e94fad3fa5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1bed0309c69d88a1723304e94fad3fa5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.tab-content','data' => ['name' => 'other']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.tab-content'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'other']); ?>
                <?php if (isset($component)) { $__componentOriginal2d35919db4362f189511ef57def4e650 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2d35919db4362f189511ef57def4e650 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.radio-group','data' => ['label' => __('settings.other.theme_mode'),'description' => __('settings.other.theme_mode_description'),'options' => $themeModeOptions,'model' => 'themeMode']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.radio-group'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.other.theme_mode')),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.other.theme_mode_description')),'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($themeModeOptions),'model' => 'themeMode']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2d35919db4362f189511ef57def4e650)): ?>
<?php $attributes = $__attributesOriginal2d35919db4362f189511ef57def4e650; ?>
<?php unset($__attributesOriginal2d35919db4362f189511ef57def4e650); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2d35919db4362f189511ef57def4e650)): ?>
<?php $component = $__componentOriginal2d35919db4362f189511ef57def4e650; ?>
<?php unset($__componentOriginal2d35919db4362f189511ef57def4e650); ?>
<?php endif; ?>

                <div class="card">
                    <?php if (isset($component)) { $__componentOriginal65bd7e7dbd93cec773ad6501ce127e46 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal65bd7e7dbd93cec773ad6501ce127e46 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.input','data' => ['type' => 'number','label' => __('settings.other.delete_old_messages'),'description' => __('settings.other.delete_old_messages_description'),'helpText' => __('settings.other.delete_old_messages_helper'),'model' => 'deleteOldMessagesDays','class' => 'w-full sm:w-40','min' => '0','step' => '1','placeholder' => '0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'number','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.other.delete_old_messages')),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.other.delete_old_messages_description')),'helpText' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.other.delete_old_messages_helper')),'model' => 'deleteOldMessagesDays','class' => 'w-full sm:w-40','min' => '0','step' => '1','placeholder' => '0']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal65bd7e7dbd93cec773ad6501ce127e46)): ?>
<?php $attributes = $__attributesOriginal65bd7e7dbd93cec773ad6501ce127e46; ?>
<?php unset($__attributesOriginal65bd7e7dbd93cec773ad6501ce127e46); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal65bd7e7dbd93cec773ad6501ce127e46)): ?>
<?php $component = $__componentOriginal65bd7e7dbd93cec773ad6501ce127e46; ?>
<?php unset($__componentOriginal65bd7e7dbd93cec773ad6501ce127e46); ?>
<?php endif; ?>
                </div>

                <div
                    class="card"
                    x-data="timezoneSelector"
                >
                    <?php if (isset($component)) { $__componentOriginal65bd7e7dbd93cec773ad6501ce127e46 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal65bd7e7dbd93cec773ad6501ce127e46 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.input','data' => ['label' => __('settings.other.timezone'),'description' => __('settings.other.timezone_description'),'helpText' => __('settings.other.timezone_helper'),'model' => 'timezone','placeholder' => __('settings.other.timezone_placeholder'),'xRef' => 'timezoneInput']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.other.timezone')),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.other.timezone_description')),'helpText' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.other.timezone_helper')),'model' => 'timezone','placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.other.timezone_placeholder')),'x-ref' => 'timezoneInput']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal65bd7e7dbd93cec773ad6501ce127e46)): ?>
<?php $attributes = $__attributesOriginal65bd7e7dbd93cec773ad6501ce127e46; ?>
<?php unset($__attributesOriginal65bd7e7dbd93cec773ad6501ce127e46); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal65bd7e7dbd93cec773ad6501ce127e46)): ?>
<?php $component = $__componentOriginal65bd7e7dbd93cec773ad6501ce127e46; ?>
<?php unset($__componentOriginal65bd7e7dbd93cec773ad6501ce127e46); ?>
<?php endif; ?>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!is_linux()): ?>
                    <?php if (isset($component)) { $__componentOriginalb5e8d8ae92d68ac68a76e18b82b039ee = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb5e8d8ae92d68ac68a76e18b82b039ee = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.toggle','data' => ['label' => __('settings.other.open_at_login'),'description' => __('settings.other.open_at_login_description'),'model' => 'openAtLogin','checked' => $openAtLogin]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.toggle'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.other.open_at_login')),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.other.open_at_login_description')),'model' => 'openAtLogin','checked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($openAtLogin)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb5e8d8ae92d68ac68a76e18b82b039ee)): ?>
<?php $attributes = $__attributesOriginalb5e8d8ae92d68ac68a76e18b82b039ee; ?>
<?php unset($__attributesOriginalb5e8d8ae92d68ac68a76e18b82b039ee); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb5e8d8ae92d68ac68a76e18b82b039ee)): ?>
<?php $component = $__componentOriginalb5e8d8ae92d68ac68a76e18b82b039ee; ?>
<?php unset($__componentOriginalb5e8d8ae92d68ac68a76e18b82b039ee); ?>
<?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="card space-y-4">
                    <?php if (isset($component)) { $__componentOriginala08669ec2d2f63b66e5592edd1656759 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala08669ec2d2f63b66e5592edd1656759 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.slider','data' => ['label' => __('settings.other.window_opacity'),'description' => __('settings.other.window_opacity_description'),'model' => 'windowOpacity','min' => '50','max' => '100','value' => $windowOpacity,'suffix' => '%']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.slider'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.other.window_opacity')),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.other.window_opacity_description')),'model' => 'windowOpacity','min' => '50','max' => '100','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($windowOpacity),'suffix' => '%']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala08669ec2d2f63b66e5592edd1656759)): ?>
<?php $attributes = $__attributesOriginala08669ec2d2f63b66e5592edd1656759; ?>
<?php unset($__attributesOriginala08669ec2d2f63b66e5592edd1656759); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala08669ec2d2f63b66e5592edd1656759)): ?>
<?php $component = $__componentOriginala08669ec2d2f63b66e5592edd1656759; ?>
<?php unset($__componentOriginala08669ec2d2f63b66e5592edd1656759); ?>
<?php endif; ?>

                    <?php if (isset($component)) { $__componentOriginala08669ec2d2f63b66e5592edd1656759 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala08669ec2d2f63b66e5592edd1656759 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.slider','data' => ['label' => __('settings.other.window_blur'),'description' => __('settings.other.window_blur_description'),'helpText' => __('settings.other.window_blur_helper'),'model' => 'windowBlur','min' => '0','max' => '100','value' => $windowBlur,'suffix' => 'px']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.slider'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.other.window_blur')),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.other.window_blur_description')),'helpText' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.other.window_blur_helper')),'model' => 'windowBlur','min' => '0','max' => '100','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($windowBlur),'suffix' => 'px']); ?>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala08669ec2d2f63b66e5592edd1656759)): ?>
<?php $attributes = $__attributesOriginala08669ec2d2f63b66e5592edd1656759; ?>
<?php unset($__attributesOriginala08669ec2d2f63b66e5592edd1656759); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala08669ec2d2f63b66e5592edd1656759)): ?>
<?php $component = $__componentOriginala08669ec2d2f63b66e5592edd1656759; ?>
<?php unset($__componentOriginala08669ec2d2f63b66e5592edd1656759); ?>
<?php endif; ?>

                    <?php if (isset($component)) { $__componentOriginalb5e8d8ae92d68ac68a76e18b82b039ee = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb5e8d8ae92d68ac68a76e18b82b039ee = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.toggle','data' => ['label' => __('settings.other.disable_transparency_maximized'),'description' => __('settings.other.disable_transparency_maximized_description'),'model' => 'disableTransparencyMaximized','checked' => $disableTransparencyMaximized,'class' => 'mt-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.toggle'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.other.disable_transparency_maximized')),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.other.disable_transparency_maximized_description')),'model' => 'disableTransparencyMaximized','checked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($disableTransparencyMaximized),'class' => 'mt-4']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb5e8d8ae92d68ac68a76e18b82b039ee)): ?>
<?php $attributes = $__attributesOriginalb5e8d8ae92d68ac68a76e18b82b039ee; ?>
<?php unset($__attributesOriginalb5e8d8ae92d68ac68a76e18b82b039ee); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb5e8d8ae92d68ac68a76e18b82b039ee)): ?>
<?php $component = $__componentOriginalb5e8d8ae92d68ac68a76e18b82b039ee; ?>
<?php unset($__componentOriginalb5e8d8ae92d68ac68a76e18b82b039ee); ?>
<?php endif; ?>
                </div>

                
                <div class="card border-2 border-red-500/20! dark:border-red-500/30! space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="shrink-0 w-10 h-10 rounded-lg bg-red-500/10 dark:bg-red-500/20 flex items-center justify-center">
                            <i class="iconoir-warning-triangle text-red-600 dark:text-red-400 text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-base font-semibold text-red-600 dark:text-red-400 mb-1">
                                <?php echo e(__('settings.danger_zone.title')); ?>

                            </h3>
                            <p class="text-sm text-slate-600 dark:text-slate-400">
                                <?php echo e(__('settings.danger_zone.description')); ?>

                            </p>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-red-500/20 dark:border-red-500/30">
                        <?php if (isset($component)) { $__componentOriginalb5e8d8ae92d68ac68a76e18b82b039ee = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb5e8d8ae92d68ac68a76e18b82b039ee = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.toggle','data' => ['label' => __('settings.danger_zone.allow_destructive_operations'),'description' => __('settings.danger_zone.allow_destructive_operations_description'),'model' => 'allowDestructiveFileOperations','checked' => $allowDestructiveFileOperations]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.toggle'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.danger_zone.allow_destructive_operations')),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('settings.danger_zone.allow_destructive_operations_description')),'model' => 'allowDestructiveFileOperations','checked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($allowDestructiveFileOperations)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb5e8d8ae92d68ac68a76e18b82b039ee)): ?>
<?php $attributes = $__attributesOriginalb5e8d8ae92d68ac68a76e18b82b039ee; ?>
<?php unset($__attributesOriginalb5e8d8ae92d68ac68a76e18b82b039ee); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb5e8d8ae92d68ac68a76e18b82b039ee)): ?>
<?php $component = $__componentOriginalb5e8d8ae92d68ac68a76e18b82b039ee; ?>
<?php unset($__componentOriginalb5e8d8ae92d68ac68a76e18b82b039ee); ?>
<?php endif; ?>
                    </div>
                </div>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1bed0309c69d88a1723304e94fad3fa5)): ?>
<?php $attributes = $__attributesOriginal1bed0309c69d88a1723304e94fad3fa5; ?>
<?php unset($__attributesOriginal1bed0309c69d88a1723304e94fad3fa5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1bed0309c69d88a1723304e94fad3fa5)): ?>
<?php $component = $__componentOriginal1bed0309c69d88a1723304e94fad3fa5; ?>
<?php unset($__componentOriginal1bed0309c69d88a1723304e94fad3fa5); ?>
<?php endif; ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal74888cb3b248a08ce228c04e2cfe93a9)): ?>
<?php $attributes = $__attributesOriginal74888cb3b248a08ce228c04e2cfe93a9; ?>
<?php unset($__attributesOriginal74888cb3b248a08ce228c04e2cfe93a9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal74888cb3b248a08ce228c04e2cfe93a9)): ?>
<?php $component = $__componentOriginal74888cb3b248a08ce228c04e2cfe93a9; ?>
<?php unset($__componentOriginal74888cb3b248a08ce228c04e2cfe93a9); ?>
<?php endif; ?>
    </div>

    
    <div
        wire:loading
        class="fixed bottom-6 right-6 z-50 flex items-center gap-2 text-xs bg-slate-500/50 p-1 px-2 text-slate-50 text-center rounded-xl"
    >
        <?php if (isset($component)) { $__componentOriginal55e350ab9b24fff3313e622c84a115ea = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal55e350ab9b24fff3313e622c84a115ea = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.loading-icon','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.loading-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal55e350ab9b24fff3313e622c84a115ea)): ?>
<?php $attributes = $__attributesOriginal55e350ab9b24fff3313e622c84a115ea; ?>
<?php unset($__attributesOriginal55e350ab9b24fff3313e622c84a115ea); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal55e350ab9b24fff3313e622c84a115ea)): ?>
<?php $component = $__componentOriginal55e350ab9b24fff3313e622c84a115ea; ?>
<?php unset($__componentOriginal55e350ab9b24fff3313e622c84a115ea); ?>
<?php endif; ?>
        <span><?php echo e(__('settings.saving')); ?></span>
    </div>
</div>
<?php /**PATH /home/bruno/Documents/projects/b7s/PurrAI/resources/views/livewire/settings.blade.php ENDPATH**/ ?>