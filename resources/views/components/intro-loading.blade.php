<!-- INTRO LOADING OVERLAY & CONTINUOUS CINEMATIC SEQUENCE (Image 2 - 5 1:1 Motion) -->
<div id="intro-stage"
     style="position: fixed; inset: 0; z-index: 99999; display: grid; place-content: center; justify-items: center; gap: 12px; text-align: center; padding: 20px; background: var(--paper, #FFFDF8); overflow: hidden;"
     aria-label="Animasi intro Velosha">

    <!-- Screen-Print Halftone Background Pattern -->
    <div style="position: absolute; inset: 0; opacity: 0.12; pointer-events: none; background-image: radial-gradient(#10282E 1.5px, transparent 1.5px); background-size: 16px 16px;"></div>

    <!-- Animated Wind & Speed Lines in Background -->
    <div class="intro-speedlines" aria-hidden="true">
        <div class="intro-sl sl-1" style="top: 20%;"></div>
        <div class="intro-sl sl-2" style="top: 28%;"></div>
        <div class="intro-sl sl-3" style="top: 68%;"></div>
        <div class="intro-sl sl-4" style="top: 76%;"></div>
    </div>

    <!-- Main Content Container -->
    <div style="position: relative; z-index: 10; max-width: 680px; width: 90%; display: flex; flex-direction: column; align-items: center;">

        <!-- Big Neobrutalist Brand Title (Stamp Effect) -->
        <h1 id="intro-logo"
            class="intro-stamp-logo"
            style="font-family: 'Bricolage Grotesque', sans-serif; font-size: clamp(64px, 13vw, 130px); font-weight: 900; line-height: 0.92; letter-spacing: -0.04em; color: #10282E; text-shadow: 6px 6px 0 #DE7119; margin: 0; user-select: none; will-change: transform, opacity;">
            VELOSHA
        </h1>

        <!-- Road Track Frame (Asphalt with Center Dashed Markings & Checkered Finish Flag) -->
        <div id="road"
             class="road"
             style="position: relative; width: min(540px, 85vw); height: 28px; margin-top: 130px; background: #10282E; border-radius: 8px; border: 2.5px solid #10282E; box-shadow: 4px 4px 0 #10282E;">
            
            <!-- Asphalt Dashed Lane Markings (Animated Conveyor) -->
            <i style="position: absolute; left: 12px; right: 34px; top: calc(50% - 2px); height: 4px; background: repeating-linear-gradient(90deg, #FFFDF8 0 18px, transparent 18px 34px); display: block;"></i>

            <!-- Checkered Finish Line at the Right Edge (100% Mark) -->
            <div class="flag"
                 aria-hidden="true"
                 style="position: absolute; right: 0; top: 0; bottom: 0; width: 24px; border-radius: 0 6px 6px 0; background: conic-gradient(#10282E 25%, #FFFDF8 0 50%, #10282E 0 75%, #FFFDF8 0) 0 0/12px 12px; border-left: 2px solid #10282E;"></div>
        </div>

        <!-- Progress Bar Below Road -->
        <div class="bar"
             style="width: min(540px, 85vw); display: flex; align-items: center; gap: 10px; margin-top: 8px;">
            <span style="flex: 1; height: 10px; border: 2.5px solid #10282E; border-radius: 8px; overflow: hidden; background: #FFFDF8; display: block;">
                <em id="fill" style="display: block; height: 100%; width: 0%; background: linear-gradient(90deg, #F4BE3F, #DE7119, #1F6F4A); transition: width 0.04s linear;"></em>
            </span>
            <b id="pct" style="font-family: 'Bricolage Grotesque', sans-serif; font-size: 15px; font-weight: 800; min-width: 42px; text-align: right; color: #10282E;">0%</b>
        </div>

    </div>

    <!-- Cyclist Running along the top of the Road Track (Asset from Cyclist_riding_road_bike_1617eabc.jpg) -->
    <div id="ibike"
         style="position: absolute; z-index: 100001; left: 0; top: 0; width: var(--bw, 140px); aspect-ratio: 780/698; pointer-events: none; display: none;">
        <!-- Speed streaks behind rear wheel -->
        <span class="sl" style="top: 48%; width: 40px;"></span>
        <span class="sl" style="top: 64%; width: 55px;"></span>
        <img src="/images/intro/cyclist_runner.png"
             alt=""
             style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: contain; filter: drop-shadow(4px 4px 0 rgba(16, 40, 46, 0.15));">
    </div>

    <!-- Controls at Bottom -->
    <button type="button"
            id="replay-intro"
            class="btn btn-ghost btn-sm"
            style="position: absolute; z-index: 100002; left: clamp(14px, 3vw, 34px); bottom: 24px; min-height: 40px; padding: 0 16px;"
            aria-label="Putar ulang animasi intro">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="mr-1"><path d="M4 12a8 8 0 1 0 3-6.2"></path><path d="M4 4v5h5"></path></svg>
        <span>Putar ulang</span>
    </button>

    <button type="button"
            id="skip-intro"
            class="btn btn-ghost btn-sm"
            style="position: absolute; z-index: 100002; right: clamp(14px, 3vw, 34px); bottom: 24px; min-height: 40px; padding: 0 16px;"
            aria-label="Lewati intro langsung ke beranda">
        <span>Lewati intro →</span>
    </button>
</div>

<!-- FLOOD LAYER: Expands from Finish line to cover screen, then shrinks into Hero Panel (Image 3 - 5) -->
<div id="flood"
     style="position: fixed; inset: 0; z-index: 99998; background: #F8E3B5; display: none; pointer-events: none;"
     aria-hidden="true"></div>
