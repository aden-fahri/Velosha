<div x-data="{
        show: !sessionStorage.getItem('velosha_intro_seen') && !window.matchMedia('(prefers-reduced-motion: reduce)').matches,
        skip() {
            this.show = false;
            try { sessionStorage.setItem('velosha_intro_seen', '1'); } catch (e) {}
        }
     }"
     x-init="
        if (show) {
            setTimeout(() => skip(), 3500);
        }
     "
     x-show="show"
     x-cloak
     x-transition:leave="transition ease-out duration-600"
     x-transition:leave-start="opacity-100 transform translate-y-0"
     x-transition:leave-end="opacity-0 transform -translate-y-8"
     id="intro-loading"
     style="position: fixed; inset: 0; z-index: 99999; background: #F6CB68; overflow: hidden;"
     aria-label="Animasi intro Velosha">

    <!-- DESKTOP SCENE (viewBox 0 0 1440 810) -->
    <div class="hidden md:block absolute inset-0 w-full h-full">
        <svg viewBox="0 0 1440 810" preserveAspectRatio="xMidYMid slice" class="w-full h-full" role="img" aria-label="Animasi pemuatan: tiga pesepeda menanjak, satu menyalip sebelum garis finis" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
            <defs>
                <mask id="md">
                    <path d="M-260,790 C200,745 560,590 860,490 S1300,220 1620,30" fill="none" stroke="#fff" stroke-width="192" pathLength="1" stroke-dasharray="1" class="vs-draw"/>
                </mask>
                <pattern id="ckd" width="18.0" height="18.0" patternUnits="userSpaceOnUse">
                    <rect width="18.0" height="18.0" fill="#FFFDF8"/>
                    <rect width="9.0" height="9.0" fill="#10282E"/>
                    <rect x="9.0" y="9.0" width="9.0" height="9.0" fill="#10282E"/>
                </pattern>
                <pattern id="cld" width="18.0" height="18.0" patternUnits="userSpaceOnUse" patternTransform="rotate(-35.7)">
                    <rect width="18.0" height="18.0" fill="#FFFDF8"/>
                    <rect width="9.0" height="9.0" fill="#10282E"/>
                    <rect x="9.0" y="9.0" width="9.0" height="9.0" fill="#10282E"/>
                </pattern>
            </defs>

            <!-- Awan Paralaks -->
            <g class="vs-cl" style="--k:0.6"><svg x="580" y="40" width="170" height="93" viewBox="80 39 161 88" overflow="hidden" aria-hidden="true"><image href="/images/mockup/scenery2.png" width="1376" height="768"/></svg></g>
            <g class="vs-cl" style="--k:0.85"><svg x="790" y="25" width="190" height="94" viewBox="382 35 199 98" overflow="hidden" aria-hidden="true"><image href="/images/mockup/scenery2.png" width="1376" height="768"/></svg></g>
            <g class="vs-cl" style="--k:1.1"><svg x="1030" y="30" width="140" height="72" viewBox="284 368 175 90" overflow="hidden" aria-hidden="true"><image href="/images/mockup/scenery2.png" width="1376" height="768"/></svg></g>
            <g class="vs-cl" style="--k:1.35"><svg x="330" y="200" width="130" height="71" viewBox="955 46 145 79" overflow="hidden" aria-hidden="true"><image href="/images/mockup/scenery2.png" width="1376" height="768"/></svg></g>

            <!-- Bukit & Gunung Belakang -->
            <g class="vs-gr">
                <path d="M-20,556 L-1,552 L17,549 L36,545 L55,541 L74,537 L92,533 L111,528 L130,524 L149,519 L167,515 L186,510 L205,505 L224,500 L242,495 L261,490 L280,484 L298,479 L317,474 L336,468 L355,462 L373,456 L392,451 L411,445 L430,439 L448,433 L467,426 L486,420 L505,414 L523,407 L542,401 L561,395 L579,388 L598,381 L617,375 L636,368 L654,362 L673,355 L692,348 L711,342 L729,335 L748,329 L767,322 L786,315 L804,309 L823,302 L842,296 L861,290 L879,283 L898,277 L917,270 L935,262 L954,254 L973,246 L992,238 L1010,229 L1029,219 L1048,209 L1067,199 L1085,189 L1104,178 L1123,166 L1142,155 L1160,143 L1179,130 L1198,118 L1216,105 L1235,92 L1254,79 L1273,65 L1291,52 L1310,38 L1329,25 L1348,11 L1366,-2 L1385,-15 L1404,-29 L1423,-42 L1441,-55 L1460,-68 L1460,830 L-20,830Z" fill="#9CCB9A"/>
                <path d="M-20,556 L-1,552 L17,549 L36,545 L55,541 L74,537 L92,533 L111,528 L130,524 L149,519 L167,515 L186,510 L205,505 L224,500 L242,495 L261,490 L280,484 L298,479 L317,474 L336,468 L355,462 L373,456 L392,451 L411,445 L430,439 L448,433 L467,426 L486,420 L505,414 L523,407 L542,401 L561,395 L579,388 L598,381 L617,375 L636,368 L654,362 L673,355 L692,348 L711,342 L729,335 L748,329 L767,322 L786,315 L804,309 L823,302 L842,296 L861,290 L879,283 L898,277 L917,270 L935,262 L954,254 L973,246 L992,238 L1010,229 L1029,219 L1048,209 L1067,199 L1085,189 L1104,178 L1123,166 L1142,155 L1160,143 L1179,130 L1198,118 L1216,105 L1235,92 L1254,79 L1273,65 L1291,52 L1310,38 L1329,25 L1348,11 L1366,-2 L1385,-15 L1404,-29 L1423,-42 L1441,-55 L1460,-68" fill="none" stroke="#10282E" stroke-width="5" stroke-linejoin="round"/>
            </g>

            <!-- Pohon & Vegetasi -->
            <g class="vs-par">
                <g class="vs-p0" style="transform-origin:170px 524px"><svg x="85" y="282" width="170" height="242" viewBox="708 35 217 309" overflow="hidden" aria-hidden="true"><image href="/images/mockup/scenery2.png" width="1376" height="768"/></svg></g>
                <g class="vs-p1" style="transform-origin:360px 471px"><svg x="290" y="308" width="140" height="163" viewBox="726 411 179 208" overflow="hidden" aria-hidden="true"><image href="/images/mockup/scenery2.png" width="1376" height="768"/></svg></g>
                <g class="vs-p2" style="transform-origin:560px 405px"><svg x="450" y="173" width="220" height="232" viewBox="1001 377 341 360" overflow="hidden" aria-hidden="true"><image href="/images/mockup/scenery2.png" width="1376" height="768"/></svg></g>
                <g class="vs-p3" style="transform-origin:740px 341px"><svg x="695" y="220" width="90" height="122" viewBox="1017 182 119 161" overflow="hidden" aria-hidden="true"><image href="/images/mockup/scenery2.png" width="1376" height="768"/></svg></g>
                <g class="vs-p4" style="transform-origin:900px 286px"><svg x="835" y="101" width="130" height="185" viewBox="708 35 217 309" overflow="hidden" aria-hidden="true"><image href="/images/mockup/scenery2.png" width="1376" height="768"/></svg></g>
                <g class="vs-p5" style="transform-origin:1040px 224px"><svg x="985" y="96" width="110" height="128" viewBox="726 411 179 208" overflow="hidden" aria-hidden="true"><image href="/images/mockup/scenery2.png" width="1376" height="768"/></svg></g>
                <g class="vs-p0" style="transform-origin:1150px 159px"><svg x="1115" y="65" width="70" height="95" viewBox="1017 182 119 161" overflow="hidden" aria-hidden="true"><image href="/images/mockup/scenery2.png" width="1376" height="768"/></svg></g>
            </g>

            <!-- Jalan Aspal Menanjak -->
            <g mask="url(#md)">
                <path d="M-260,790 C200,745 560,590 860,490 S1300,220 1620,30" fill="none" stroke="#10282E" stroke-width="87" stroke-linecap="round"/>
                <path d="M-260,790 C200,745 560,590 860,490 S1300,220 1620,30" fill="none" stroke="#B2590F" stroke-width="77" stroke-linecap="round"/>
                <g fill="none" stroke="#FFFDF8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M-260,790 L-196,783" stroke-width="10.1"/>
                    <path d="M-129,774 L-68,764" stroke-width="9.8"/>
                    <path d="M-8,753 L51,742" stroke-width="9.5"/>
                    <path d="M109,729 L165,715" stroke-width="9.3"/>
                    <path d="M221,701 L267,688" stroke-width="9.0"/>
                    <path d="M328,670 L373,657" stroke-width="8.8"/>
                    <path d="M428,639 L478,623" stroke-width="8.5"/>
                    <path d="M527,606 L568,592" stroke-width="8.3"/>
                    <path d="M619,574 L659,560" stroke-width="8.1"/>
                    <path d="M707,543 L752,527" stroke-width="7.9"/>
                    <path d="M796,512 L833,499" stroke-width="7.7"/>
                    <path d="M878,484 L918,469" stroke-width="7.5"/>
                    <path d="M959,452 L991,438" stroke-width="7.3"/>
                    <path d="M1032,418 L1066,400" stroke-width="7.1"/>
                    <path d="M1101,379 L1134,359" stroke-width="6.9"/>
                    <path d="M1166,339 L1193,321" stroke-width="6.8"/>
                    <path d="M1227,298 L1254,279" stroke-width="6.6"/>
                    <path d="M1284,257 L1311,238" stroke-width="6.4"/>
                    <path d="M1339,217 L1363,200" stroke-width="6.3"/>
                    <path d="M1393,179 L1415,163" stroke-width="6.1"/>
                    <path d="M1444,143 L1468,127" stroke-width="6.0"/>
                    <path d="M1495,109 L1517,94" stroke-width="5.9"/>
                    <path d="M1543,77 L1564,64" stroke-width="5.7"/>
                    <path d="M1592,47 L1614,34" stroke-width="5.6"/>
                </g>
            </g>

            <!-- Path Animasi Sepeda -->
            <path id="pd" d="M-260,790 C200,745 560,590 860,490 S1300,220 1620,30" fill="none"/>

            <!-- Rambu & Papan Jarak (1 KM & 500 M) -->
            <g class="vs-p5" style="transform-origin:611px 488px">
                <line x1="611" y1="488" x2="611" y2="424" stroke="#10282E" stroke-width="6" stroke-linecap="round"/>
                <g transform="translate(611,410)">
                    <rect x="-44" y="-19" width="88" height="38" rx="7" fill="#FFFDF8" stroke="#10282E" stroke-width="4"/>
                    <text y="7" text-anchor="middle" font-family="'Bricolage Grotesque', sans-serif" font-weight="800" font-size="22" fill="#10282E">1 KM</text>
                </g>
            </g>
            <g class="vs-p0" style="transform-origin:972px 353px">
                <line x1="972" y1="353" x2="972" y2="289" stroke="#10282E" stroke-width="6" stroke-linecap="round"/>
                <g transform="translate(972,275)">
                    <rect x="-44" y="-19" width="88" height="38" rx="7" fill="#FFFDF8" stroke="#10282E" stroke-width="4"/>
                    <text y="7" text-anchor="middle" font-family="'Bricolage Grotesque', sans-serif" font-weight="800" font-size="22" fill="#10282E">500 M</text>
                </g>
            </g>

            <!-- Gerbang Finis -->
            <g class="vs-gate">
                <path d="M1252,225 L1277,208 L1329,280 L1304,297Z" fill="url(#cld)" stroke="#10282E" stroke-width="3"/>
                <line x1="1256" y1="205" x2="1256" y2="55" stroke="#10282E" stroke-width="10" stroke-linecap="round"/>
                <line x1="1256" y1="205" x2="1256" y2="55" stroke="#FFFDF8" stroke-width="4" stroke-linecap="round"/>
                <g class="vs-flag" style="transform-origin:1256px 81px">
                    <rect x="1256" y="55" width="84" height="52" fill="url(#ckd)" stroke="#10282E" stroke-width="4"/>
                </g>
                <line x1="1325" y1="300" x2="1325" y2="150" stroke="#10282E" stroke-width="10" stroke-linecap="round"/>
                <line x1="1325" y1="300" x2="1325" y2="150" stroke="#FFFDF8" stroke-width="4" stroke-linecap="round"/>
                <g class="vs-flag" style="transform-origin:1325px 176px">
                    <rect x="1325" y="150" width="84" height="52" fill="url(#ckd)" stroke="#10282E" stroke-width="4"/>
                </g>
                <g transform="translate(1250,29)">
                    <rect x="-58" y="-17" width="116" height="34" rx="6" fill="#FFFDF8" stroke="#10282E" stroke-width="4"/>
                    <text y="7" text-anchor="middle" font-family="'Bricolage Grotesque', sans-serif" font-weight="800" font-size="22" fill="#10282E">FINISH</text>
                </g>
            </g>

            <!-- PESEPEDA 1: BASE ORANGE (Durasi 3.5s) -->
            <g>
                <animateMotion dur="3.5s" repeatCount="1" fill="freeze" rotate="auto" calcMode="linear" keyPoints="0;0.0000;1.0000;1" keyTimes="0;0.0800;0.6800;1">
                    <mpath href="#pd" xlink:href="#pd"/>
                </animateMotion>
                <g transform="translate(0,-8) scale(0.376)">
                    <g class="vs-fxg" aria-hidden="true">
                        <svg x="-960" y="-330" width="360" height="57" viewBox="165 438 365 58" overflow="hidden"><image href="/images/mockup/fx.png" xlink:href="/images/mockup/fx.png" width="1376" height="768"/></svg>
                        <svg x="-900" y="-230" width="300" height="56" viewBox="728 441 308 57" overflow="hidden"><image href="/images/mockup/fx.png" xlink:href="/images/mockup/fx.png" width="1376" height="768"/></svg>
                        <svg x="-840" y="-130" width="300" height="52" viewBox="309 610 326 56" overflow="hidden"><image href="/images/mockup/fx.png" xlink:href="/images/mockup/fx.png" width="1376" height="768"/></svg>
                    </g>
                    <image class="spind0" href="/images/mockup/spokes_r.png" xlink:href="/images/mockup/spokes_r.png" x="-349.5" y="-282.5" width="256" height="256" style="transform-box:fill-box;transform-origin:center"/>
                    <image class="spind0" href="/images/mockup/spokes_f.png" xlink:href="/images/mockup/spokes_f.png" x="92.5" y="-284.5" width="256" height="256" style="transform-box:fill-box;transform-origin:center"/>
                    <image href="/images/mockup/bike_base.png" xlink:href="/images/mockup/bike_base.png" x="-375" y="-670" width="750" height="670"/>
                </g>
            </g>

            <!-- PESEPEDA 2: PURPLE (Durasi 3.5s) -->
            <g>
                <animateMotion dur="3.5s" repeatCount="1" fill="freeze" rotate="auto" calcMode="linear" keyPoints="0;0.0000;0.0000;0.9600;1" keyTimes="0;0.0800;0.1200;0.7000;1">
                    <mpath href="#pd" xlink:href="#pd"/>
                </animateMotion>
                <g transform="translate(0,6) scale(0.357)">
                    <image class="spind1" href="/images/mockup/spokes_r.png" xlink:href="/images/mockup/spokes_r.png" x="-349.5" y="-282.5" width="256" height="256" style="transform-box:fill-box;transform-origin:center"/>
                    <image class="spind1" href="/images/mockup/spokes_f.png" xlink:href="/images/mockup/spokes_f.png" x="92.5" y="-284.5" width="256" height="256" style="transform-box:fill-box;transform-origin:center"/>
                    <image href="/images/mockup/bike_p.png" xlink:href="/images/mockup/bike_p.png" x="-375" y="-670" width="750" height="670"/>
                </g>
            </g>

            <!-- PESEPEDA 3: GREEN (MENYALIP DI FINIS - Durasi 3.5s) -->
            <g>
                <animateMotion dur="3.5s" repeatCount="1" fill="freeze" rotate="auto" calcMode="linear" keyPoints="0;0.0000;0.4200;0.6600;0.9800;1.0000;1" keyTimes="0;0.0800;0.4000;0.5400;0.6500;0.6800;1">
                    <mpath href="#pd" xlink:href="#pd"/>
                </animateMotion>
                <g transform="translate(0,21) scale(0.369)">
                    <g class="vs-fxg" aria-hidden="true">
                        <svg x="-960" y="-330" width="360" height="57" viewBox="165 438 365 58" overflow="hidden"><image href="/images/mockup/fx.png" xlink:href="/images/mockup/fx.png" width="1376" height="768"/></svg>
                    </g>
                    <image class="spind2" href="/images/mockup/spokes_r.png" xlink:href="/images/mockup/spokes_r.png" x="-349.5" y="-282.5" width="256" height="256" style="transform-box:fill-box;transform-origin:center"/>
                    <image class="spind2" href="/images/mockup/spokes_f.png" xlink:href="/images/mockup/spokes_f.png" x="92.5" y="-284.5" width="256" height="256" style="transform-box:fill-box;transform-origin:center"/>
                    <image href="/images/mockup/bike_g.png" xlink:href="/images/mockup/bike_g.png" x="-375" y="-670" width="750" height="670"/>
                </g>
            </g>

            <!-- Lapisan Rumput Depan -->
            <g class="vs-gr2">
                <path d="M-20,828 L1460,204 L1460,830 L-20,830Z" fill="#1F6F4A" stroke="#10282E" stroke-width="5" stroke-linejoin="round"/>
            </g>
        </svg>

        <!-- Watermark Brand Velosha -->
        <div class="vs-wm" style="position: absolute; left: 72px; top: 56px; padding: 6px 12px 10px 0; pointer-events: none">
            <div style="font: 800 84px/1 'Bricolage Grotesque', sans-serif; letter-spacing: -.03em; color: #FFFDF8; -webkit-text-stroke: 8px #10282E; paint-order: stroke fill; text-shadow: 4px 4px 0 #10282E">Velosha</div>
            <div style="margin-top: 12px; font: 700 16px 'Inter', sans-serif; letter-spacing: .18em; color: #17583A">CYCLING MARATHON &amp; TICKETING</div>
        </div>
    </div>

    <!-- MOBILE SCENE (viewBox 0 0 390 780) -->
    <div class="block md:hidden absolute inset-0 w-full h-full">
        <svg viewBox="0 0 390 780" preserveAspectRatio="xMidYMid slice" class="w-full h-full" role="img" aria-label="Animasi intro HP" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
            <defs>
                <mask id="mm">
                    <path d="M-200,740 C0,700 150,580 230,470 S360,220 520,20" fill="none" stroke="#fff" stroke-width="88" pathLength="1" stroke-dasharray="1" class="vs-draw"/>
                </mask>
                <pattern id="ckm" width="10.8" height="10.8" patternUnits="userSpaceOnUse">
                    <rect width="10.8" height="10.8" fill="#FFFDF8"/>
                    <rect width="5.4" height="5.4" fill="#10282E"/>
                    <rect x="5.4" y="5.4" width="5.4" height="5.4" fill="#10282E"/>
                </pattern>
                <pattern id="clm" width="10.8" height="10.8" patternUnits="userSpaceOnUse" patternTransform="rotate(-60.0)">
                    <rect width="10.8" height="10.8" fill="#FFFDF8"/>
                    <rect width="5.4" height="5.4" fill="#10282E"/>
                    <rect x="5.4" y="5.4" width="5.4" height="5.4" fill="#10282E"/>
                </pattern>
            </defs>

            <!-- Awan Paralaks HP -->
            <g class="vs-cl" style="--k:0.6"><svg x="190" y="30" width="120" height="59" viewBox="382 35 199 98" overflow="hidden"><image href="/images/mockup/scenery2.png" xlink:href="/images/mockup/scenery2.png" width="1376" height="768"/></svg></g>
            <g class="vs-cl" style="--k:0.85"><svg x="40" y="170" width="90" height="49" viewBox="955 46 145 79" overflow="hidden"><image href="/images/mockup/scenery2.png" xlink:href="/images/mockup/scenery2.png" width="1376" height="768"/></svg></g>

            <!-- Bukit & Pohon HP -->
            <g class="vs-gr">
                <path d="M-20,558 L410,52 L410,800 L-20,800Z" fill="#9CCB9A" stroke="#10282E" stroke-width="5" stroke-linejoin="round"/>
            </g>

            <!-- Jalan HP -->
            <g mask="url(#mm)">
                <path d="M-200,740 C0,700 150,580 230,470 S360,220 520,20" fill="none" stroke="#10282E" stroke-width="45" stroke-linecap="round"/>
                <path d="M-200,740 C0,700 150,580 230,470 S360,220 520,20" fill="none" stroke="#B2590F" stroke-width="35" stroke-linecap="round"/>
            </g>

            <path id="pm" d="M-200,740 C0,700 150,580 230,470 S360,220 520,20" fill="none"/>

            <!-- Gerbang Finis HP -->
            <g class="vs-gate">
                <path d="M267,373 L276,358 L313,379 L304,394Z" fill="url(#clm)" stroke="#10282E" stroke-width="2"/>
                <line x1="264" y1="361" x2="264" y2="271" stroke="#10282E" stroke-width="6" stroke-linecap="round"/>
                <g class="vs-flag" style="transform-origin:264px 287px">
                    <rect x="264" y="271" width="50" height="31" fill="url(#ckm)" stroke="#10282E" stroke-width="2"/>
                </g>
                <g transform="translate(260,256)">
                    <rect x="-35" y="-10" width="70" height="20" rx="4" fill="#FFFDF8" stroke="#10282E" stroke-width="2"/>
                    <text y="4" text-anchor="middle" font-family="'Bricolage Grotesque', sans-serif" font-weight="800" font-size="13" fill="#10282E">FINISH</text>
                </g>
            </g>

            <!-- Pesepeda HP -->
            <g>
                <animateMotion dur="3.5s" repeatCount="1" fill="freeze" rotate="auto" calcMode="linear" keyPoints="0;0.0000;1.0000;1" keyTimes="0;0.0800;0.6800;1">
                    <mpath href="#pm" xlink:href="#pm"/>
                </animateMotion>
                <g transform="translate(0,0) scale(0.238)">
                    <image class="spind0" href="/images/mockup/spokes_r.png" xlink:href="/images/mockup/spokes_r.png" x="-349.5" y="-282.5" width="256" height="256"/>
                    <image class="spind0" href="/images/mockup/spokes_f.png" xlink:href="/images/mockup/spokes_f.png" x="92.5" y="-284.5" width="256" height="256"/>
                    <image href="/images/mockup/bike_base.png" xlink:href="/images/mockup/bike_base.png" x="-375" y="-670" width="750" height="670"/>
                </g>
            </g>

            <!-- Green Jersey Menyusul HP -->
            <g>
                <animateMotion dur="3.5s" repeatCount="1" fill="freeze" rotate="auto" calcMode="linear" keyPoints="0;0.0000;0.4200;0.6600;0.9800;1.0000;1" keyTimes="0;0.0800;0.4000;0.5400;0.6500;0.6800;1">
                    <mpath href="#pm" xlink:href="#pm"/>
                </animateMotion>
                <g transform="translate(0,13) scale(0.233)">
                    <image class="spind2" href="/images/mockup/spokes_r.png" xlink:href="/images/mockup/spokes_r.png" x="-349.5" y="-282.5" width="256" height="256"/>
                    <image class="spind2" href="/images/mockup/spokes_f.png" xlink:href="/images/mockup/spokes_f.png" x="92.5" y="-284.5" width="256" height="256"/>
                    <image href="/images/mockup/bike_g.png" xlink:href="/images/mockup/bike_g.png" x="-375" y="-670" width="750" height="670"/>
                </g>
            </g>

            <!-- Rumput Depan HP -->
            <g class="vs-gr2">
                <path d="M-20,714 L410,208 L410,800 L-20,800Z" fill="#1F6F4A" stroke="#10282E" stroke-width="5" stroke-linejoin="round"/>
            </g>
        </svg>

        <div class="vs-wm" style="position: absolute; left: 24px; top: 40px; pointer-events: none">
            <div style="font: 800 48px/1 'Bricolage Grotesque', sans-serif; letter-spacing: -.03em; color: #FFFDF8; -webkit-text-stroke: 4px #10282E; paint-order: stroke fill; text-shadow: 3px 3px 0 #10282E">Velosha</div>
            <div style="margin-top: 8px; font: 700 11px 'Inter', sans-serif; letter-spacing: .18em; color: #17583A">CYCLING MARATHON &amp; TICKETING</div>
        </div>
    </div>

    <!-- Tombol Lewati Intro di Kanan Bawah -->
    <button type="button" @click="skip()" class="skip-intro" style="right: 24px; bottom: 24px;" aria-label="Lewati intro pemuatan">
        <span>Lewati intro</span>
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10282E" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
    </button>
</div>
