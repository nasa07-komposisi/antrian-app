@extends('layouts.app')

@section('title', 'Tampilan Publik')

@section('content')
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card bg-dark text-white p-3 text-center shadow-sm border-0">
                <h2 class="fw-bold text-uppercase mb-1" style="letter-spacing: 2px;">Informasi Antrian Kantor Pajak Wates
                </h2>
                <p class="small mb-0 opacity-75">Silakan menuju loket saat nomor Anda dipanggil</p>
            </div>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-4 row-cols-lg-7 g-2 mb-4 px-1" id="counter-cards">
        @foreach($counters as $counter)
            <div class="col">
                @php
                    $color = $counter->service->color_class ?? 'primary';
                    $hex = $counter->service->hex_color ?? '#0d6efd';
                @endphp
                <div class="card h-100 border-3 shadow-sm scale-hover" style="border-color: {{ $hex }} !important;">
                    <div class="card-header text-white text-center py-2" style="background-color: {{ $hex }} !important;">
                        <h4 class="mb-0 fw-bold">{{ $counter->name }}</h4>
                        <small class="badge bg-white mt-1 px-2 py-1" style="color: {{ $hex }} !important; font-size: 0.7rem;">
                            {{ $counter->service->name ?? '-' }}
                        </small>
                    </div>
                    <div class="card-body text-center d-flex flex-column justify-content-center py-3">
                        <small class="text-muted mb-1 fw-bold letter-spacing-1" style="font-size: 0.75rem;">SEDANG
                            MELAYANI</small>
                        <div class="display-3 fw-bold mb-2 current-queue-number" style="color: {{ $hex }} !important;">
                            {{ $counter->queues->first()?->queue_number ?? '---' }}
                        </div>
                    </div>
                    <div class="card-footer bg-light border-0 py-2">
                        <div class="row text-center g-0">
                            <div class="col-6 border-end">
                                <small class="text-muted d-block text-uppercase fw-bold"
                                    style="font-size: 0.65rem;">Menunggu</small>
                                @php
                                    $waitingCount = \App\Models\Queue::where('service_id', $counter->service_id)
                                        ->where('status', 'waiting')
                                        ->count();
                                @endphp
                                <h5 class="mb-0 fw-bold waiting-count-number">{{ $waitingCount }}</h5>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block text-uppercase fw-bold"
                                    style="font-size: 0.65rem;">Status</small>
                                <span class="badge rounded-pill"
                                    style="font-size: 0.65rem; background-color: {{ $counter->status == 'busy' ? '#198754' : '#6c757d' }}">
                                    {{ $counter->status == 'busy' ? 'AKTIF' : 'OFFLINE' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Alur Layanan Pelanggan (Compact) -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm bg-white rounded-4 overflow-hidden">
                <div class="card-body py-2 px-4">
                    <div class="row text-center align-items-center">
                        <div class="col text-start border-end">
                            <span class="text-info fw-bold me-2"><i class="bi bi-info-circle-fill"></i> ALUR:</span>
                        </div>
                        <div class="col">
                            <div class="d-flex align-items-center justify-content-center gap-2 py-1">
                                <div class="bg-info-subtle text-info rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 24px; height: 24px;">
                                    <small class="fw-bold">1</small>
                                </div>
                                <small class="fw-bold text-dark">Ambil Antrian</small>
                            </div>
                        </div>
                        <div class="col-auto"><i class="bi bi-chevron-right text-muted small"></i></div>
                        <div class="col">
                            <div class="d-flex align-items-center justify-content-center gap-2 py-1">
                                <div class="bg-info-subtle text-info rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 24px; height: 24px;">
                                    <small class="fw-bold">2</small>
                                </div>
                                <small class="fw-bold text-dark">Tunggu Panggilan</small>
                            </div>
                        </div>
                        <div class="col-auto"><i class="bi bi-chevron-right text-muted small"></i></div>
                        <div class="col">
                            <div class="d-flex align-items-center justify-content-center gap-2 py-1">
                                <div class="bg-info-subtle text-info rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 24px; height: 24px;">
                                    <small class="fw-bold">3</small>
                                </div>
                                <small class="fw-bold text-dark">Menuju Loket</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Pelayanan Hari Ini -->
    <div class="row mt-4 mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-header bg-dark text-white py-3">
                    <h5 class="mb-0 text-center text-uppercase fw-bold letter-spacing-1">
                        <i class="bi bi-graph-up-arrow me-2" style="color: var(--gold-accent);"></i> Statistik Pelayanan
                        Hari Ini
                    </h5>
                </div>
                <div class="card-body bg-white py-4">
                    <div class="row g-4 justify-content-center" id="services-summary">
                        @php
                            $allServices = \App\Models\Service::all();
                        @endphp
                        @foreach($allServices as $svc)
                            <div class="col-6 col-md-3">
                                <div class="text-center p-3 border rounded-3 bg-light h-100">
                                    <div class="h1 fw-bold mb-0" style="color: {{ $svc->hex_color }}">
                                        {{ \App\Models\Queue::where('service_id', $svc->id)->where('status', 'finished')->whereDate('finished_at', today())->count() }}
                                    </div>
                                    <div class="text-muted small text-uppercase fw-bold">{{ $svc->name }}</div>
                                    <div class="small text-muted mt-1">Selesai Terlayani</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Announcement Bar -->
    <div class="announcement-bar shadow-lg" id="announcement-container" style="display: none;">
        <div class="announcement-content">
            <i class="bi bi-megaphone-fill me-2" style="color: var(--gold-accent);"></i>
            <span id="announcement-text" style="color: white;">Selamat Datang di Kantor Pajak Wates</span>
        </div>
    </div>

    <!-- Fullscreen Toggle Button Overlay -->
    <div class="fullscreen-overlay" id="fullscreen-btn" onclick="toggleFullscreen()" title="Layar Penuh">
        <i class="bi bi-arrows-fullscreen"></i>
    </div>

    <!-- Audio Activation Overlay Removed -->

    @push('scripts')
        <script>
            let lastCalled = {}; // Store last called queue_id per counter
            let lastCalledTime = {}; // Store last called timestamp per counter
            let audioContext = null;
            let currentAnnouncements = [];
            let announcementIdx = 0;
            let announcementInterval = null;

            window.activateAudio = function () {
                console.log("Activating audio...");

                try {
                    // Initialize AudioContext on user interaction
                    if (!audioContext) {
                        audioContext = new (window.AudioContext || window.webkitAudioContext)();
                    }

                    if (audioContext.state === 'suspended') {
                        audioContext.resume();
                    }

                    // Play a dummy short silent sound to "prime" the audio
                    const osc = audioContext.createOscillator();
                    const gain = audioContext.createGain();
                    gain.gain.value = 0.01;
                    osc.connect(gain);
                    gain.connect(audioContext.destination);
                    osc.start(0);
                    osc.stop(0.1);

                    // Update Navbar Button
                    const btn = document.getElementById('btn-activate-audio');
                    if (btn) {
                        btn.classList.replace('btn-warning', 'btn-success');
                        btn.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> SUARA AKTIF';
                        btn.disabled = true;
                    }

                    // Test Speech
                    speak("Sistem suara aktif");
                    console.log("Audio Activated Successfully");
                } catch (e) {
                    console.error("Audio Activation Error:", e);
                }
            };

            // Speech Synthesis Setup
            const synth = window.speechSynthesis;

            function playChime() {
                if (!audioContext) return;

                const playTone = (freq, start, duration) => {
                    const osc = audioContext.createOscillator();
                    const gain = audioContext.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(freq, audioContext.currentTime + start);
                    gain.gain.setValueAtTime(0.1, audioContext.currentTime + start);
                    gain.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + start + duration);
                    osc.connect(gain);
                    gain.connect(audioContext.destination);
                    osc.start(audioContext.currentTime + start);
                    osc.stop(audioContext.currentTime + start + duration);
                };

                // Classic Ding-Dong
                playTone(554.37, 0, 1.0); // C#5
                playTone(440.00, 0.5, 1.0); // A4
            }

            function speak(text) {
                if (!synth) return;
                if (synth.speaking) {
                    synth.cancel();
                }

                const utter = new SpeechSynthesisUtterance(text);

                utter.onend = function () {
                    console.log("Speech finished, unlocking call...");
                    fetch('{{ route("counter.unlock-call") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    }).catch(e => console.error("Unlock error:", e));
                };

                utter.onerror = function (event) {
                    console.error("Speech error, unlocking call...", event);
                    fetch('{{ route("counter.unlock-call") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    }).catch(e => console.error("Unlock error:", e));
                };

                const getIndoFemaleVoice = () => {
                    const voices = synth.getVoices();

                    // 1. HIGHEST PRIORITY: Google Bahasa Indonesia
                    let voice = voices.find(v => v.name === 'Google Bahasa Indonesia' || (v.name.includes('Google') && v.lang === 'id-ID'));

                    // 2. Second Priority: Specific high-quality names
                    if (!voice) {
                        const targetNames = ['Gadis', 'Andika', 'Laila', 'Indonesian Female'];
                        voice = voices.find(v => v.lang === 'id-ID' && targetNames.some(name => v.name.includes(name)));
                    }

                    // 3. Fallback to any ID-ID voice
                    if (!voice) voice = voices.find(v => v.lang === 'id-ID');

                    return voice;
                };

                const setVoice = () => {
                    const voice = getIndoFemaleVoice();
                    if (voice) {
                        utter.voice = voice;
                        console.log("Using Voice:", voice.name);
                    }
                    utter.lang = 'id-ID';
                    utter.rate = 0.90;
                    utter.pitch = 1.0;
                    synth.speak(utter);
                };

                // If voices are not loaded yet, wait for them
                if (synth.getVoices().length === 0) {
                    synth.onvoiceschanged = setVoice;
                } else {
                    setVoice();
                }
            }

            function announceQueue(prefix, number, counterName) {
                let numStr = number.toString();
                let spelledNum = numStr.split('').map(n => n === '0' ? 'kosong' : n).join(', ');

                const message = `Nomor, antrian. ${prefix}. ${spelledNum}. silakan menuju ke, ${counterName}.`;

                playChime();

                setTimeout(() => {
                    speak(message);
                }, 1600);
            }

            // Sync update function
            function updateStatus() {
                fetch('{{ route("public.status") }}')
                    .then(response => response.json())
                    .then(data => {
                        const counters = data.counters;
                        const summary = data.summary;

                        // Update Counter Cards
                        const cardContainer = document.getElementById('counter-cards');
                        counters.forEach((item, index) => {
                            // Detect new call OR recall
                            if (item.queue_id && (lastCalled[item.counter_name] !== item.queue_id || lastCalledTime[item.counter_name] !== item.called_at)) {
                                if (lastCalled[item.counter_name] !== undefined) {
                                    announceQueue(item.prefix, item.current_number, item.counter_name);
                                }
                                lastCalled[item.counter_name] = item.queue_id;
                                lastCalledTime[item.counter_name] = item.called_at;
                            } else if (!item.queue_id) {
                                lastCalled[item.counter_name] = null;
                            }

                            const card = cardContainer.children[index];
                            if (card) {
                                const display = card.querySelector('.current-queue-number');
                                if (display) display.innerText = item.current_queue;

                                const waiting = card.querySelector('.waiting-count-number');
                                if (waiting) waiting.innerText = item.waiting_count;

                                const statusBadge = card.querySelector('.badge.rounded-pill');
                                if (statusBadge) {
                                    if (item.status === 'busy') {
                                        statusBadge.innerText = 'AKTIF';
                                        statusBadge.classList.replace('bg-secondary', 'bg-success');
                                    } else {
                                        statusBadge.innerText = 'OFFLINE';
                                        statusBadge.classList.replace('bg-success', 'bg-secondary');
                                    }
                                }
                            }
                        });

                        // Update Services Summary
                        const summaryContainer = document.getElementById('services-summary');
                        if (summaryContainer) {
                            summary.forEach((svc, idx) => {
                                const svcCard = summaryContainer.children[idx];
                                if (svcCard) {
                                    const countEl = svcCard.querySelector('.h1');
                                    if (countEl) countEl.innerText = svc.finished_count;
                                }
                            });
                        }

                        // Update Announcements
                        if (data.announcements && data.announcements.length > 0) {
                            if (JSON.stringify(currentAnnouncements) !== JSON.stringify(data.announcements)) {
                                currentAnnouncements = data.announcements;
                                startAnnouncements();
                            }
                        } else {
                            currentAnnouncements = [];
                            document.getElementById('announcement-container').style.display = 'none';
                            if (announcementInterval) clearInterval(announcementInterval);
                        }
                    })
                    .catch(err => console.error('Error fetching status:', err));
            }

            function startAnnouncements() {
                if (announcementInterval) clearInterval(announcementInterval);
                const container = document.getElementById('announcement-container');
                const textEl = document.getElementById('announcement-text');

                if (currentAnnouncements.length === 0) {
                    container.style.display = 'none';
                    return;
                }

                container.style.display = 'flex';
                announcementIdx = 0;

                const rotate = () => {
                    // Trigger animation by removing and adding class or changing text with transition
                    container.classList.remove('animate-up');
                    void container.offsetWidth; // trigger reflow

                    textEl.innerText = currentAnnouncements[announcementIdx];
                    container.classList.add('animate-up');

                    announcementIdx = (announcementIdx + 1) % currentAnnouncements.length;
                };

                rotate();
                announcementInterval = setInterval(rotate, 10000); // Rotate every 10 seconds (5s show + 5s hide)
            }

            // Fullscreen Logic
            window.toggleFullscreen = function () {
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen().catch(err => {
                        console.error(`Error enabling full-screen mode: ${err.message}`);
                    });
                } else {
                    if (document.exitFullscreen) {
                        document.exitFullscreen();
                    }
                }
            };

            document.addEventListener('fullscreenchange', () => {
                const btn = document.getElementById('fullscreen-btn');
                if (document.fullscreenElement) {
                    btn.innerHTML = '<i class="bi bi-fullscreen-exit"></i>';
                    btn.classList.add('active');
                } else {
                    btn.innerHTML = '<i class="bi bi-arrows-fullscreen"></i>';
                    btn.classList.remove('active');
                }
            });

            // Initialization
            setInterval(updateStatus, 3000);

            // Try load voices immediately
            synth.getVoices();
        </script>
        <style>
            .scale-hover:hover {
                transform: scale(1.02);
                transition: all 0.2s ease;
            }

            .letter-spacing-1 {
                letter-spacing: 1px;
            }

            .btn-xl {
                padding: 1.5rem 2rem;
                font-size: 1.5rem;
                border-radius: 1rem;
            }

            @media (min-width: 992px) {
                .row-cols-lg-7>* {
                    flex: 0 0 auto;
                    width: 14.2857142857%;
                }
            }

            .fullscreen-overlay {
                position: fixed;
                bottom: 30px;
                right: 30px;
                width: 65px;
                height: 65px;
                background-color: rgba(0, 0, 0, 0.4);
                backdrop-filter: blur(5px);
                color: white;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 24px;
                cursor: pointer;
                z-index: 9999;
                transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
                border: 2px solid rgba(255, 255, 255, 0.2);
            }

            .fullscreen-overlay:hover {
                background-color: #0d6efd;
                transform: scale(1.1) translateY(-5px);
                box-shadow: 0 12px 20px rgba(13, 110, 253, 0.3);
                border-color: rgba(255, 255, 255, 0.5);
            }

            .fullscreen-overlay.active {
                background-color: rgba(220, 53, 69, 0.6);
            }

            .fullscreen-overlay.active:hover {
                background-color: #dc3545;
            }

            /* Announcement Bar styles */
            .announcement-bar {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                height: 60px;
                background: linear-gradient(90deg, var(--navy-blue), #000044);
                color: white;
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 9998;
                padding: 0 20px;
                border-top: 3px solid var(--gold-accent);
            }

            .announcement-content {
                font-size: 1.4rem;
                font-weight: 600;
                text-align: center;
            }

            .animate-up {
                animation: slideUp 10s infinite;
            }

            @keyframes slideUp {
                0% {
                    transform: translateY(100%);
                    opacity: 0;
                }

                5% {
                    transform: translateY(0);
                    opacity: 1;
                }

                50% {
                    transform: translateY(0);
                    opacity: 1;
                }

                55% {
                    transform: translateY(-100%);
                    opacity: 0;
                }

                100% {
                    transform: translateY(-100%);
                    opacity: 0;
                }
            }

            /* Adjust main content padding to prevent overlap with announcement bar */
            body {
                padding-bottom: 70px;
            }
        </style>
    @endpush
@endsection