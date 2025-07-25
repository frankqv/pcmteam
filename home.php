<?php

 ?>
<!DOCTYPE html>
<html lang="es" class="h-100">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css"
        integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <meta name="description" content="Author: FrankQV,
    DEVELOPER: FRANKQV MAYO 2025, Tecnologia, Category: sistema, Price: $900.99 Dollar,">
    <title>PCMARKETTEAM</title>
    <link rel="icon" type="image/png" href="backend/img/favicon.png">
    <!-- Hotjar Tracking Code for PcMarketTEAM -->
    <script>
        (function(h,o,t,j,a,r){
            h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
            h._hjSettings={hjid:6474228,hjsv:6};
            a=o.getElementsByTagName('head')[0];
            r=o.createElement('script');r.async=1;
            r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
            a.appendChild(r);
        })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
    </script>
    <!-- script Mapa de calor -->
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', sans-serif;
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
        color: white;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    /* Header Styles */
    .main-header {
        position: fixed;
        top: 0;
        width: 100%;
        background: rgba(26, 26, 46, 0.95);
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(73, 182, 159, 0.2);
        z-index: 1000;
        padding: 1rem 0;
        transition: all 0.3s ease;
    }

    .header-container {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 2rem;
    }

    .logo h1 {
        font-size: 1.8rem;
        font-weight: 700;
        letter-spacing: -0.5px;
    }

    .logo span {
        color: #2B6B5D;
        background: linear-gradient(45deg, #2B6B5D, #00dc92);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .nav-menu {
        display: flex;
        list-style: none;
        gap: 2rem;
        align-items: center;
    }

    .nav-menu a {
        color: white;
        text-decoration: none;
        font-weight: 500;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        transition: all 0.3s ease;
        text-transform: capitalize;
    }

    .nav-menu a:hover {
        background: rgba(73, 182, 159, 0.1);
        color: #2B6B5D;
        transform: translateY(-2px);
    }

    .social-links {
        display: flex;
        gap: 1rem;
    }

    .social-links a {
        color: white;
        font-size: 1.2rem;
        padding: 0.5rem;
        border-radius: 50%;
        transition: all 0.3s ease;
        background: rgba(255, 255, 255, 0.1);
    }

    .social-links a:hover {
        background: #2B6B5D;
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(73, 182, 159, 0.4);
    }

    .menu-toggle {
        display: none;
        background: none;
        border: none;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
    }

    /* Hero Section */
    .hero-section {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 8rem 2rem 4rem;
        position: relative;
        overflow: hidden;
    }

    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: radial-gradient(circle at 50% 50%, rgba(73, 182, 159, 0.1) 0%, transparent 50%);
        z-index: -1;
    }

    .hero-content {
        max-width: 800px;
        animation: fadeInUp 1s ease-out;
    }

    .hero-title {
        font-size: 3.5rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        line-height: 1.2;
        background: linear-gradient(45deg, #ffffff, #2B6B5D);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .hero-subtitle {
        font-size: 1.3rem;
        margin-bottom: 2rem;
        opacity: 0.9;
        line-height: 1.6;
        font-weight: 300;
    }

    .cta-button {
        display: inline-block;
        background: linear-gradient(45deg, #2B6B5D, #00dc92);
        color: white;
        padding: 1rem 2.5rem;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        box-shadow: 0 10px 30px rgba(73, 182, 159, 0.3);
    }

    .cta-button:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 40px rgba(73, 182, 159, 0.4);
        color: white;
    }

    /* Contact Section */
    .contact-section {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        padding: 4rem 2rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .contact-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .contact-title {
        text-align: center;
        font-size: 2.5rem;
        font-weight: 600;
        margin-bottom: 3rem;
        color: #2B6B5D;
    }

    .contact-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
        margin-bottom: 2rem;
    }

    .contact-item {
        background: rgba(255, 255, 255, 0.1);
        padding: 2rem;
        border-radius: 15px;
        text-align: center;
        transition: all 0.3s ease;
        border: 1px solid rgba(73, 182, 159, 0.2);
    }

    .contact-item:hover {
        transform: translateY(-5px);
        background: rgba(73, 182, 159, 0.1);
        box-shadow: 0 10px 30px rgba(73, 182, 159, 0.2);
    }

    .contact-item i {
        font-size: 2.5rem;
        color: #00dc92;
        margin-bottom: 1rem;
        display: block;
    }

    .contact-item h3 {
        font-size: 1.2rem;
        margin-bottom: 0.5rem;
        color: #2B6B5D;
    }

    .contact-item p {
        opacity: 0.9;
        line-height: 1.6;
    }

    /* Footer */
    .main-footer {
        background: rgba(0, 0, 0, 0.3);
        padding: 2rem;
        text-align: center;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        margin-top: auto;
    }

    .footer-content {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .footer-text {
        opacity: 0.8;
    }

    .footer-text span {
        color: #2B6B5D;
        font-weight: 600;
    }

    /* Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .header-container {
            padding: 0 1rem;
        }

        .nav-menu {
            position: fixed;
            top: 100%;
            left: 0;
            width: 100%;
            background: rgba(26, 26, 46, 0.98);
            flex-direction: column;
            padding: 2rem;
            transition: top 0.3s ease;
            backdrop-filter: blur(20px);
        }

        .nav-menu.active {
            top: 100%;
        }

        .menu-toggle {
            display: block;
        }

        .hero-title {
            font-size: 2.5rem;
        }

        .hero-subtitle {
            font-size: 1.1rem;
        }

        .contact-grid {
            grid-template-columns: 1fr;
        }

        .footer-content {
            flex-direction: column;
            text-align: center;
        }
    }

    @media (max-width: 480px) {
        .hero-section {
            padding: 6rem 1rem 3rem;
        }

        .hero-title {
            font-size: 2rem;
        }

        .contact-section {
            padding: 3rem 1rem;
        }
    }

    /* Scroll Behavior */
    html {
        scroll-behavior: smooth;
    }

    /* Custom Scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #1a1a2e;
    }

    ::-webkit-scrollbar-thumb {
        background: #2B6B5D;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #00dc92;
    }
    </style>
</head>

<body>
    <!-- Header -->
    <header class="main-header">
        <div class="header-container">
            <div class="logo">
                <h1><span>PCMARKET</span>TEAM</h1>
            </div>

            <nav class="nav-menu" id="navMenu">
                <a href="#inicio" onclick="closeMenu()">Inicio</a>
                <a href="#contacto" onclick="closeMenu()">Contacto</a>
                <a href="frontend/login.php" onclick="closeMenu()">Ingresar</a>
            </nav>

            <div class="social-links">
                <a href="https://api.whatsapp.com/send/?phone=573222024365&text=%C2%A1Hola!%20%F0%9F%91%8B%20Bienvenido%2Fa%20%F0%9F%98%8E%0A%0ASoy%20FrankQV%2C%20el%20desarrollador%20detr%C3%A1s%20de%20este%20sistema.%20%F0%9F%9A%80%0A%0AAdem%C3%A1s%20de%20construir%20soluciones%20como%20esta%2C%20tambi%C3%A9n%20ofrezco%3A%0A%E2%9C%85%20Desarrollo%20de%20p%C3%A1ginas%20web%0A%E2%9C%85%20Sistemas%20a%20medida%0A%E2%9C%85%20Automatizaciones%20y%20m%C3%A1s%0A%0AJuntos%20podemos%20llevar%20la%20tecnolog%C3%ADa%20a%20otro%20nivel%20%F0%9F%9A%80%F0%9F%98%8E%0A%0A%C2%BFEn%20qu%C3%A9%20puedo%20ayudarte%20hoy%3F%20%F0%9F%92%AC"
                    target="_blank" rel="noopener noreferrer" title="WhatsApp">
                    <i class="fa-brands fa-whatsapp"></i>
                </a>
                <a href="https://www.tiktok.com/@pcmarkett" target="_blank" rel="noopener noreferrer" title="TikTok">
                    <i class="fa-brands fa-tiktok"></i>
                </a>
                <a href="https://www.youtube.com/@PCMARKETT" target="_blank" rel="noopener noreferrer" title="YouTube">
                    <i class="fa-brands fa-youtube"></i>
                </a>
                <a href="https://github.com/frankqv" target="_blank" rel="noopener noreferrer" title="YouTube">
                    <i class="fa-brans fa-github"></i>
                </a>


            </div>

            <button class="menu-toggle" id="menuToggle" onclick="toggleMenu()">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>
    </header>

    <!-- Hero Section -->
    <main class="hero-section" style="background:#215247;" id="inicio">
        <div class="hero-content">
            <h1 class="hero-title">Bienvenido a PCMARKETTEAM</h1>
            <p class="hero-subtitle">
                Tu destino tecnológico de confianza. Ofrecemos las mejores soluciones en hardware y software
                con el respaldo de expertos en tecnología. Transformamos tus ideas en realidad digital.
            </p>
            <a href="#contacto" class="cta-button">
                <i class="fas fa-rocket"></i> Comenzar Ahora
            </a>
        </div>
        <!-- Hero Image -->
    </main>

    <section id="inicio" class="contacto">
        <div class="fila">
            <!-- xml version="1.0" encoding="UTF-8" -->
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                viewBox="0 0 452.47 301.65">
                <defs>
                    <style>
                    .cls-1 {
                        fill: url(#linear-gradient-15);
                    }

                    .cls-2 {
                        fill: url(#linear-gradient-13);
                    }

                    .cls-3 {
                        fill: #e3aa00;
                    }

                    .cls-4 {
                        fill: url(#linear-gradient-2);
                    }

                    .cls-5 {
                        fill: url(#linear-gradient-10);
                    }

                    .cls-6 {
                        fill: url(#linear-gradient-12);
                    }

                    .cls-7 {
                        fill: #fff;
                    }

                    .cls-8 {
                        mask: url(#mask);
                    }

                    .cls-9 {
                        fill: #f59091;
                    }

                    .cls-10,
                    .cls-11,
                    .cls-12 {
                        fill: none;
                    }

                    .cls-13 {
                        fill: url(#linear-gradient-4);
                    }

                    .cls-14 {
                        fill: #aca7c4;
                    }

                    .cls-11 {
                        mix-blend-mode: overlay;
                        opacity: .22;
                        stroke: #fff;
                        stroke-width: 8.55px;
                    }

                    .cls-11,
                    .cls-12 {
                        stroke-miterlimit: 10;
                    }

                    .cls-15 {
                        fill: #00dc92;
                    }

                    .cls-16 {
                        fill: #ffa6a3;
                    }

                    .cls-17 {
                        fill: #190075;
                    }

                    .cls-18 {
                        fill: #e1eae8;
                    }

                    .cls-19 {
                        fill: #a86342;
                    }

                    .cls-20 {
                        fill: url(#linear-gradient-3);
                    }

                    .cls-21 {
                        clip-path: url(#clippath-1);
                    }

                    .cls-22 {
                        fill: url(#linear-gradient-5);
                    }

                    .cls-23 {
                        fill: url(#linear-gradient-22);
                    }

                    .cls-24 {
                        fill: #ab96ff;
                    }

                    .cls-25 {
                        isolation: isolate;
                    }

                    .cls-26 {
                        fill: #2a2e5e;
                    }

                    .cls-27 {
                        fill: #8f4928;
                    }

                    .cls-12 {
                        stroke: #002032;
                    }

                    .cls-28 {
                        fill: url(#linear-gradient-8);
                    }

                    .cls-29 {
                        fill: #eff4df;
                    }

                    .cls-30 {
                        fill: #ffbab8;
                    }

                    .cls-31 {
                        fill: #d1c7ff;
                    }

                    .cls-32 {
                        fill: #210099;
                    }

                    .cls-33 {
                        fill: url(#linear-gradient-14);
                    }

                    .cls-34 {
                        fill: url(#linear-gradient-20);
                    }

                    .cls-35 {
                        fill: url(#linear-gradient-17);
                    }

                    .cls-36 {
                        fill: url(#linear-gradient-7);
                    }

                    .cls-37 {
                        fill: url(#linear-gradient-9);
                    }

                    .cls-38 {
                        fill: url(#linear-gradient-11);
                    }

                    .cls-39 {
                        fill: url(#linear-gradient-19);
                    }

                    .cls-40 {
                        fill: url(#linear-gradient-6);
                    }

                    .cls-41 {
                        fill: url(#linear-gradient-21);
                    }

                    .cls-42 {
                        fill: #f06d42;
                    }

                    .cls-43 {
                        fill: #f6f6f7;
                    }

                    .cls-44 {
                        opacity: .19;
                    }

                    .cls-45 {
                        opacity: .28;
                    }

                    .cls-46 {
                        fill: url(#linear-gradient-16);
                    }

                    .cls-47 {
                        fill: #ffbf00;
                    }

                    .cls-48 {
                        fill: url(#linear-gradient);
                    }

                    .cls-49 {
                        clip-path: url(#clippath);
                    }

                    .cls-50 {
                        fill: #9a87e6;
                    }

                    .cls-51 {
                        fill: url(#linear-gradient-18);
                    }
                    </style>
                    <linearGradient id="linear-gradient" x1="0" y1="150.82" x2="303.33" y2="150.82"
                        gradientUnits="userSpaceOnUse">
                        <stop offset="0" stop-color="aqua" />
                        <stop offset="1" stop-color="#00aaf6" />
                    </linearGradient>
                    <clipPath id="clippath">
                        <path class="cls-10"
                            d="M207.47,78.17l10.18,78.7H84.81l-9.71-78.7h132.36M211.86,73.17H69.45l.69,5.61,9.71,78.7.54,4.39h142.95l-.73-5.64-10.18-78.7-.56-4.36h0Z" />
                    </clipPath>
                    <clipPath id="clippath-1">
                        <rect class="cls-10" x="-310.38" y="-110.35" width="166.62" height="166.62" />
                    </clipPath>
                    <linearGradient id="linear-gradient-2" x1="-510.4" y1="62.02" x2="-510.4" y2="52.96"
                        gradientTransform="translate(901.57)" gradientUnits="userSpaceOnUse">
                        <stop offset="0" stop-color="#8cf08f" />
                        <stop offset="1" stop-color="#00b69e" />
                    </linearGradient>
                    <linearGradient id="linear-gradient-3" x1="-506.28" y1="54.23" x2="-506.28" y2="51.51"
                        xlink:href="#linear-gradient-2" />
                    <linearGradient id="linear-gradient-4" x1="-514.2" y1="54.23" x2="-514.2" y2="51.51"
                        xlink:href="#linear-gradient-2" />
                    <linearGradient id="linear-gradient-5" x1="-510.24" y1="52.47" x2="-510.24" y2="49.75"
                        xlink:href="#linear-gradient-2" />
                    <mask id="mask" x="370.63" y="34.63" width="45.61" height="41.46" maskUnits="userSpaceOnUse">
                        <path class="cls-7"
                            d="M370.63,34.63v41.46h45.28v-41.46h-45.28ZM395.47,71.13h-18.64v-26.46h18.64v26.46ZM414.3,49.82c0,.14,0,.29-.01.44l.97.32c.36.12.65.37.82.7.17.34.2.72.08,1.08l-.47,1.43c-.19.58-.73.97-1.34.97h0c-.15,0-.29-.02-.44-.07l-.97-.32c-.16.24-.33.48-.52.71l.6.83c.22.3.31.68.25,1.05-.06.37-.26.7-.56.92l-1.22.89c-.24.18-.53.27-.83.27-.45,0-.88-.22-1.14-.58l-.6-.83c-.27.1-.55.19-.83.27v1.02c0,.78-.63,1.41-1.41,1.41h-1.51c-.78,0-1.41-.63-1.41-1.41v-1.02c-.28-.08-.56-.17-.83-.27l-.6.83c-.26.36-.69.58-1.14.58-.3,0-.59-.09-.83-.27l-1.22-.89c-.3-.22-.51-.55-.56-.92-.06-.37.03-.75.25-1.05l.6-.83c-.18-.23-.36-.47-.52-.71l-.97.32c-.14.05-.29.07-.44.07-.61,0-1.15-.39-1.34-.98l-.47-1.43c-.24-.74.17-1.54.91-1.78l.97-.32c0-.15-.01-.3-.01-.44s0-.29.01-.44l-.97-.32c-.74-.24-1.15-1.04-.91-1.78l.47-1.43c.19-.58.73-.98,1.34-.98.15,0,.29.02.44.07l.97.32c.16-.24.33-.48.52-.71l-.6-.83c-.22-.3-.31-.68-.25-1.05.06-.37.26-.7.56-.92l1.22-.89c.24-.18.53-.27.83-.27.45,0,.88.22,1.14.58l.6.83c.27-.1.55-.2.83-.27v-1.02c0-.78.63-1.41,1.41-1.41h1.51c.78,0,1.41.63,1.41,1.41v1.02c.28.07.56.17.83.27l.6-.83c.26-.36.69-.58,1.14-.58.3,0,.59.09.83.27l1.22.89c.3.22.51.55.56.92.06.37-.03.74-.25,1.05l-.6.83c.18.23.36.47.52.71l.97-.32c.14-.05.29-.07.44-.07.61,0,1.15.39,1.34.98l.47,1.43c.12.36.09.74-.08,1.08-.17.34-.46.58-.82.7l-.97.32c0,.15.01.29.01.44Z" />
                    </mask>
                    <linearGradient id="linear-gradient-6" x1="-524.03" y1="37.22" x2="-486.46" y2="81.88"
                        gradientTransform="translate(901.57)" xlink:href="#linear-gradient" />
                    <linearGradient id="linear-gradient-7" x1="-508.67" y1="42.26" x2="-508.67" y2="71.97"
                        xlink:href="#linear-gradient-2" />
                    <linearGradient id="linear-gradient-8" x1="-508.67" y1="38.18" x2="-508.67" y2="72.46"
                        xlink:href="#linear-gradient-2" />
                    <linearGradient id="linear-gradient-9" x1="-495.62" y1="40.84" x2="-495.62" y2="58.38"
                        gradientTransform="translate(901.57)" xlink:href="#linear-gradient" />
                    <linearGradient id="linear-gradient-10" x1="-495.62" y1="40.84" x2="-495.62" y2="58.38"
                        gradientTransform="translate(901.57)" xlink:href="#linear-gradient" />
                    <linearGradient id="linear-gradient-11" x1="385.92" y1="81.79" x2="385.92" y2="85.44"
                        gradientTransform="matrix(1,0,0,1,0,0)" xlink:href="#linear-gradient-2" />
                    <linearGradient id="linear-gradient-12" x1="385.92" y1="81.79" x2="385.92" y2="85.44"
                        gradientTransform="matrix(1,0,0,1,0,0)" xlink:href="#linear-gradient-2" />
                    <linearGradient id="linear-gradient-13" x1="385.92" y1="81.79" x2="385.92" y2="85.44"
                        gradientTransform="matrix(1,0,0,1,0,0)" xlink:href="#linear-gradient-2" />
                    <linearGradient id="linear-gradient-14" x1="385.92" y1="81.79" x2="385.92" y2="85.44"
                        gradientTransform="matrix(1,0,0,1,0,0)" xlink:href="#linear-gradient-2" />
                    <linearGradient id="linear-gradient-15" x1="385.92" y1="81.79" x2="385.92" y2="85.44"
                        gradientTransform="matrix(1,0,0,1,0,0)" xlink:href="#linear-gradient-2" />
                    <linearGradient id="linear-gradient-16" x1="385.92" y1="81.79" x2="385.92" y2="85.44"
                        gradientTransform="matrix(1,0,0,1,0,0)" xlink:href="#linear-gradient-2" />
                    <linearGradient id="linear-gradient-17" x1="385.92" y1="81.79" x2="385.92" y2="85.44"
                        gradientTransform="matrix(1,0,0,1,0,0)" xlink:href="#linear-gradient-2" />
                    <linearGradient id="linear-gradient-18" x1="385.92" y1="81.79" x2="385.92" y2="85.44"
                        gradientTransform="matrix(1,0,0,1,0,0)" xlink:href="#linear-gradient-2" />
                    <linearGradient id="linear-gradient-19" x1="412.22" y1="81.93" x2="412.22" y2="84.64"
                        xlink:href="#linear-gradient" />
                    <linearGradient id="linear-gradient-20" x1="412.22" y1="81.93" x2="412.22" y2="84.64"
                        xlink:href="#linear-gradient" />
                    <linearGradient id="linear-gradient-21" x1="412.22" y1="81.93" x2="412.22" y2="84.64"
                        xlink:href="#linear-gradient" />
                    <linearGradient id="linear-gradient-22" x1="412.22" y1="81.93" x2="412.22" y2="84.64"
                        xlink:href="#linear-gradient" />
                </defs>
                <g class="cls-25">
                    <path class="cls-18"
                        d="M366.06,145.37c.06.07.1.15.1.25,0,.1-.03.19-.1.25-.06.07-.15.1-.25.1s-.18-.03-.25-.1c-.06-.06-.1-.15-.1-.25s.03-.18.1-.25c.06-.07.15-.1.25-.1s.19.03.25.1ZM365.71,146.68h.25s.06.02.06.06l.04,2.82s-.02.06-.06.06h-.32s-.06-.02-.06-.06l.03-2.82s.02-.06.06-.06Z" />
                    <path class="cls-18"
                        d="M367.97,149.57c-.22-.11-.39-.28-.52-.48-.12-.21-.18-.45-.18-.72v-1.72c0-.27.06-.51.18-.72.12-.21.29-.37.52-.48.22-.11.48-.17.77-.17s.54.06.77.17c.22.11.39.27.52.47.12.2.18.43.18.68v.08s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-.08c0-.28-.09-.51-.28-.68-.19-.17-.43-.26-.74-.26s-.56.09-.74.27c-.19.18-.28.41-.28.71v1.75c0,.29.1.53.29.71.19.18.44.27.76.27s.55-.08.73-.25c.18-.17.27-.39.27-.67v-.57s0-.02-.03-.02h-.92s-.06-.02-.06-.06v-.26s.02-.06.06-.06h1.32s.06.02.06.06v.81c0,.45-.13.79-.4,1.04s-.62.38-1.07.38c-.29,0-.55-.06-.77-.17Z" />
                    <path class="cls-18"
                        d="M372.78,146.58s.05.04.04.08l-.07.31s-.04.05-.08.04c-.07-.03-.15-.04-.24-.04h-.08c-.22.01-.4.09-.54.25s-.21.34-.21.58v1.84s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-3.04s.02-.06.06-.06h.32s.06.02.06.06v.38s0,.02,0,.02c0,0,.01,0,.02,0,.09-.15.2-.27.33-.36.14-.09.29-.13.47-.13.14,0,.26.03.36.08Z" />
                    <path class="cls-18"
                        d="M375.38,146.77c.2.19.31.45.31.76v2.1s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-.26s0-.01,0-.02c0,0-.01,0-.02,0-.1.12-.24.22-.41.28-.17.06-.35.1-.56.1-.3,0-.54-.08-.74-.22-.2-.15-.3-.38-.3-.69s.11-.56.34-.74c.23-.18.54-.27.95-.27h.72s.03,0,.03-.03v-.24c0-.21-.06-.38-.18-.5-.12-.12-.3-.18-.55-.18-.2,0-.36.04-.49.12s-.2.18-.23.32c-.01.04-.04.06-.08.06l-.34-.04s-.07-.03-.06-.05c.03-.23.16-.42.38-.57.22-.15.49-.22.81-.22.38,0,.66.1.87.29ZM374.97,149.18c.18-.12.27-.29.27-.5v-.49s0-.03-.03-.03h-.66c-.27,0-.5.06-.66.17-.17.11-.25.27-.25.47,0,.18.06.32.19.42s.29.14.5.14c.25,0,.46-.06.64-.18Z" />
                    <path class="cls-18"
                        d="M377.03,149.51c-.21-.16-.36-.37-.44-.63-.05-.18-.08-.44-.08-.78,0-.3.03-.55.08-.76.08-.26.22-.47.44-.62s.47-.23.77-.23.57.08.79.23.37.35.43.58c.02.08.03.14.04.19h0s-.02.06-.06.07l-.31.04h-.01s-.05-.02-.06-.06l-.02-.09c-.04-.16-.13-.29-.28-.4-.15-.11-.32-.17-.53-.17s-.38.06-.52.17-.23.26-.27.44c-.04.16-.06.37-.06.63,0,.28.02.49.06.64.04.19.13.34.27.45s.31.17.52.17.38-.05.53-.16c.15-.11.24-.24.28-.41v-.05s.04-.06.08-.05l.31.05s.06.03.06.07l-.02.12c-.06.24-.21.44-.43.59-.22.15-.49.22-.79.22s-.56-.08-.77-.23Z" />
                    <path class="cls-18"
                        d="M379.91,145.86c-.07-.07-.1-.15-.1-.25s.03-.19.1-.25.15-.1.25-.1.18.03.25.1.1.15.1.25-.03.18-.1.25c-.07.07-.15.1-.25.1s-.18-.03-.25-.1ZM379.92,149.63v-3.04s.02-.06.06-.06h.32s.06.02.06.06v3.04s-.02.06-.06.06h-.32s-.06-.02-.06-.06Z" />
                    <path class="cls-18"
                        d="M383.42,146.77c.2.19.31.45.31.76v2.1s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-.26s0-.01,0-.02c0,0-.01,0-.02,0-.1.12-.24.22-.41.28-.17.06-.35.1-.56.1-.3,0-.54-.08-.74-.22-.2-.15-.3-.38-.3-.69s.11-.56.34-.74c.23-.18.54-.27.95-.27h.72s.03,0,.03-.03v-.24c0-.21-.06-.38-.18-.5-.12-.12-.3-.18-.55-.18-.2,0-.36.04-.49.12s-.2.18-.23.32c-.01.04-.04.06-.08.06l-.34-.04s-.07-.03-.06-.05c.03-.23.16-.42.38-.57.22-.15.49-.22.81-.22.38,0,.66.1.87.29ZM383.01,149.18c.18-.12.27-.29.27-.5v-.49s0-.03-.03-.03h-.66c-.27,0-.5.06-.66.17-.17.11-.25.27-.25.47,0,.18.06.32.19.42s.29.14.5.14c.25,0,.46-.06.64-.18Z" />
                    <path class="cls-18"
                        d="M385.06,149.61c-.18-.08-.32-.17-.42-.3-.1-.12-.15-.26-.15-.42v-.08s.02-.06.06-.06h.3s.06.02.06.06v.05c0,.14.07.26.21.36.14.1.32.15.54.15s.39-.05.53-.15c.13-.1.2-.22.2-.37,0-.1-.03-.19-.1-.26-.07-.07-.15-.12-.24-.16-.09-.04-.24-.08-.43-.14-.23-.07-.42-.13-.57-.2-.15-.07-.28-.16-.38-.28-.1-.12-.15-.27-.15-.45,0-.27.1-.48.31-.64.21-.16.48-.24.82-.24.23,0,.43.04.61.11.18.08.31.18.41.31.1.13.14.28.14.44v.02s-.02.06-.06.06h-.29s-.06-.02-.06-.06v-.02c0-.14-.07-.26-.2-.36s-.32-.14-.55-.14c-.21,0-.38.04-.51.13-.13.09-.19.2-.19.35,0,.14.06.25.19.32.12.08.32.15.58.23.24.07.44.14.59.2.15.06.28.15.39.27s.16.27.16.47c0,.27-.11.49-.32.65-.21.16-.49.24-.84.24-.23,0-.44-.04-.62-.11Z" />
                    <path class="cls-18"
                        d="M391.45,148.11c0,.33-.03.59-.08.77-.08.27-.21.48-.41.63-.2.15-.45.23-.75.23-.16,0-.31-.03-.45-.1s-.25-.16-.34-.28c0,0-.02-.01-.02,0,0,0,0,0,0,.02v1.47s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-4.25s.02-.06.06-.06h.32s.06.02.06.06v.26s0,.01,0,.02c0,0,.01,0,.02,0,.09-.12.21-.21.34-.28s.29-.1.45-.1c.29,0,.54.08.73.23.2.15.33.35.41.61.06.2.09.46.09.79ZM390.97,148.59c.02-.11.03-.27.03-.48s0-.35-.02-.46c-.02-.11-.05-.21-.09-.3-.05-.15-.14-.27-.27-.36-.13-.09-.28-.13-.45-.13s-.31.04-.42.13c-.11.09-.2.2-.25.35-.04.08-.06.18-.08.29s-.02.27-.02.47,0,.36.02.47.05.22.09.3c.05.14.13.26.25.34.11.09.25.13.41.13.17,0,.31-.04.43-.12.12-.08.21-.19.27-.33.04-.09.08-.19.1-.31Z" />
                    <path class="cls-18"
                        d="M392.74,149.51c-.22-.15-.37-.37-.45-.64-.06-.19-.09-.45-.09-.77s.03-.57.09-.76c.08-.27.23-.48.44-.63s.48-.23.79-.23.55.08.77.23c.21.15.36.36.44.62.06.18.09.44.09.77s-.03.59-.09.77c-.08.27-.23.48-.44.64-.21.15-.47.23-.77.23s-.56-.08-.77-.23ZM394.03,149.19c.14-.11.24-.26.29-.45.04-.15.06-.36.06-.63s-.02-.49-.05-.63c-.05-.19-.15-.34-.29-.45s-.32-.17-.52-.17-.38.06-.52.17c-.14.11-.24.26-.29.45-.03.15-.05.36-.05.63s.02.49.05.63c.05.19.14.34.28.45.14.11.32.17.53.17s.37-.05.51-.17Z" />
                    <path class="cls-18"
                        d="M397.35,146.58s.05.04.04.08l-.07.31s-.04.05-.08.04c-.07-.03-.15-.04-.24-.04h-.08c-.22.01-.4.09-.54.25s-.21.34-.21.58v1.84s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-3.04s.02-.06.06-.06h.32s.06.02.06.06v.38s0,.02,0,.02c0,0,.01,0,.02,0,.09-.15.2-.27.33-.36.14-.09.29-.13.47-.13.14,0,.26.03.36.08Z" />
                    <path class="cls-18"
                        d="M399.97,149.65l-1.03-3.04v-.03s.01-.04.05-.04h.36s.06.02.08.05l.78,2.45s.01.01.02.01c0,0,.01,0,.02-.01l.78-2.45s.04-.05.08-.05h.36s.06.03.05.07l-1.02,3.04s-.04.05-.08.05h-.36s-.06-.02-.08-.05Z" />
                    <path class="cls-18"
                        d="M402.26,145.86c-.07-.07-.1-.15-.1-.25s.03-.19.1-.25.15-.1.25-.1.18.03.25.1.1.15.1.25-.03.18-.1.25c-.07.07-.15.1-.25.1s-.18-.03-.25-.1ZM402.27,149.63v-3.04s.02-.06.06-.06h.32s.06.02.06.06v3.04s-.02.06-.06.06h-.32s-.06-.02-.06-.06Z" />
                    <path class="cls-18"
                        d="M404.22,149.61c-.18-.08-.32-.17-.42-.3-.1-.12-.15-.26-.15-.42v-.08s.02-.06.06-.06h.3s.06.02.06.06v.05c0,.14.07.26.21.36.14.1.32.15.54.15s.39-.05.53-.15c.13-.1.2-.22.2-.37,0-.1-.03-.19-.1-.26-.07-.07-.15-.12-.24-.16-.09-.04-.24-.08-.43-.14-.23-.07-.42-.13-.57-.2-.15-.07-.28-.16-.38-.28-.1-.12-.15-.27-.15-.45,0-.27.1-.48.31-.64.21-.16.48-.24.82-.24.23,0,.43.04.61.11.18.08.31.18.41.31.1.13.14.28.14.44v.02s-.02.06-.06.06h-.29s-.06-.02-.06-.06v-.02c0-.14-.07-.26-.2-.36s-.32-.14-.55-.14c-.21,0-.38.04-.51.13-.13.09-.19.2-.19.35,0,.14.06.25.19.32.12.08.32.15.58.23.24.07.44.14.59.2.15.06.28.15.39.27s.16.27.16.47c0,.27-.11.49-.32.65-.21.16-.49.24-.84.24-.23,0-.44-.04-.62-.11Z" />
                    <path class="cls-18"
                        d="M406.86,145.86c-.07-.07-.1-.15-.1-.25s.03-.19.1-.25.15-.1.25-.1.18.03.25.1.1.15.1.25-.03.18-.1.25c-.07.07-.15.1-.25.1s-.18-.03-.25-.1ZM406.87,149.63v-3.04s.02-.06.06-.06h.32s.06.02.06.06v3.04s-.02.06-.06.06h-.32s-.06-.02-.06-.06Z" />
                    <path class="cls-18"
                        d="M409.76,146.9h-.71s-.03,0-.03.03v1.84c0,.2.04.34.13.42.09.08.23.12.42.12h.16s.06.02.06.06v.26s-.02.06-.06.06c-.05,0-.13,0-.23,0-.3,0-.53-.06-.68-.17-.15-.11-.23-.32-.23-.62v-1.98s0-.03-.02-.03h-.38s-.06-.02-.06-.06v-.24s.02-.06.06-.06h.38s.02,0,.02-.03v-.72s.02-.06.06-.06h.31s.06.02.06.06v.72s0,.03.03.03h.71s.06.02.06.06v.24s-.02.06-.06.06Z" />
                    <path class="cls-18"
                        d="M412.64,146.77c.2.19.31.45.31.76v2.1s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-.26s0-.01,0-.02c0,0-.01,0-.02,0-.1.12-.24.22-.41.28-.17.06-.35.1-.56.1-.3,0-.54-.08-.74-.22-.2-.15-.3-.38-.3-.69s.11-.56.34-.74c.23-.18.54-.27.95-.27h.72s.03,0,.03-.03v-.24c0-.21-.06-.38-.18-.5-.12-.12-.3-.18-.55-.18-.2,0-.36.04-.49.12s-.2.18-.23.32c-.01.04-.04.06-.08.06l-.34-.04s-.07-.03-.06-.05c.03-.23.16-.42.38-.57.22-.15.49-.22.81-.22.38,0,.66.1.87.29ZM412.23,149.18c.18-.12.27-.29.27-.5v-.49s0-.03-.03-.03h-.66c-.27,0-.5.06-.66.17-.17.11-.25.27-.25.47,0,.18.06.32.19.42s.29.14.5.14c.25,0,.46-.06.64-.18Z" />
                    <path class="cls-18"
                        d="M415.5,146.58s.05.04.04.08l-.07.31s-.04.05-.08.04c-.07-.03-.15-.04-.24-.04h-.08c-.22.01-.4.09-.54.25s-.21.34-.21.58v1.84s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-3.04s.02-.06.06-.06h.32s.06.02.06.06v.38s0,.02,0,.02c0,0,.01,0,.02,0,.09-.15.2-.27.33-.36.14-.09.29-.13.47-.13.14,0,.26.03.36.08Z" />
                    <path class="cls-18"
                        d="M418.26,146.78c.2.2.3.46.3.79v2.06s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-1.97c0-.23-.07-.42-.21-.56-.14-.15-.32-.22-.54-.22s-.42.07-.56.21-.21.33-.21.56v1.99s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-3.04s.02-.06.06-.06h.32s.06.02.06.06v.27s0,.01,0,.02.01,0,.02,0c.18-.26.46-.39.84-.39.33,0,.6.1.8.29Z" />
                    <path class="cls-18"
                        d="M419.9,149.51c-.22-.15-.37-.37-.45-.64-.06-.19-.09-.45-.09-.77s.03-.57.09-.76c.08-.27.23-.48.44-.63s.48-.23.79-.23.55.08.77.23c.21.15.36.36.44.62.06.18.09.44.09.77s-.03.59-.09.77c-.08.27-.23.48-.44.64-.21.15-.47.23-.77.23s-.56-.08-.77-.23ZM421.19,149.19c.14-.11.24-.26.29-.45.04-.15.06-.36.06-.63s-.02-.49-.05-.63c-.05-.19-.15-.34-.29-.45s-.32-.17-.52-.17-.38.06-.52.17c-.14.11-.24.26-.29.45-.03.15-.05.36-.05.63s.02.49.05.63c.05.19.14.34.28.45.14.11.32.17.53.17s.37-.05.51-.17Z" />
                    <path class="cls-18"
                        d="M423.29,149.61c-.18-.08-.32-.17-.42-.3-.1-.12-.15-.26-.15-.42v-.08s.02-.06.06-.06h.3s.06.02.06.06v.05c0,.14.07.26.21.36.14.1.32.15.54.15s.39-.05.53-.15c.13-.1.2-.22.2-.37,0-.1-.03-.19-.1-.26-.07-.07-.15-.12-.24-.16-.09-.04-.24-.08-.43-.14-.23-.07-.42-.13-.57-.2-.15-.07-.28-.16-.38-.28-.1-.12-.15-.27-.15-.45,0-.27.1-.48.31-.64.21-.16.48-.24.82-.24.23,0,.43.04.61.11.18.08.31.18.41.31.1.13.14.28.14.44v.02s-.02.06-.06.06h-.29s-.06-.02-.06-.06v-.02c0-.14-.07-.26-.2-.36s-.32-.14-.55-.14c-.21,0-.38.04-.51.13-.13.09-.19.2-.19.35,0,.14.06.25.19.32.12.08.32.15.58.23.24.07.44.14.59.2.15.06.28.15.39.27s.16.27.16.47c0,.27-.11.49-.32.65-.21.16-.49.24-.84.24-.23,0-.44-.04-.62-.11Z" />
                    <path class="cls-18"
                        d="M426.12,149.57c-.06-.06-.1-.15-.1-.25s.03-.19.1-.25.15-.1.25-.1.19.03.25.1.1.15.1.25-.03.18-.1.25-.15.1-.25.1-.18-.03-.25-.1ZM426.2,148.21l-.03-2.83s.02-.06.06-.06h.32s.06.02.06.06l-.04,2.83s-.02.06-.06.06h-.25s-.06-.02-.06-.06Z" />
                    <path class="cls-18"
                        d="M325.12,161.96h-2.3s-.03,0-.03.02v1.54s0,.03.03.03h1.61s.06.02.06.06v.26s-.02.06-.06.06h-1.61s-.03,0-.03.02v1.57s0,.02.03.02h2.3s.06.02.06.06v.26s-.02.06-.06.06h-2.71s-.06-.02-.06-.06v-4.25s.02-.06.06-.06h2.71s.06.02.06.06v.26s-.02.06-.06.06Z" />
                    <path class="cls-18"
                        d="M326.46,165.86c-.18-.08-.32-.17-.42-.3-.1-.12-.15-.26-.15-.42v-.08s.02-.06.06-.06h.3s.06.02.06.06v.05c0,.14.07.26.21.36.14.1.32.15.54.15s.39-.05.53-.15c.13-.1.2-.22.2-.37,0-.1-.03-.19-.1-.26-.07-.07-.15-.12-.24-.16-.09-.04-.24-.08-.43-.14-.23-.07-.42-.13-.57-.2-.15-.07-.28-.16-.38-.28-.1-.12-.15-.27-.15-.45,0-.27.1-.48.31-.64.21-.16.48-.24.82-.24.23,0,.43.04.61.11.18.08.31.18.41.31.1.13.14.28.14.44v.02s-.02.06-.06.06h-.29s-.06-.02-.06-.06v-.02c0-.14-.07-.26-.2-.36s-.32-.14-.55-.14c-.21,0-.38.04-.51.13-.13.09-.19.2-.19.35,0,.14.06.25.19.32.12.08.32.15.58.23.24.07.44.14.59.2.15.06.28.15.39.27s.16.27.16.47c0,.27-.11.49-.32.65-.21.16-.49.24-.84.24-.23,0-.44-.04-.62-.11Z" />
                    <path class="cls-18"
                        d="M330.4,163.15h-.71s-.03,0-.03.03v1.84c0,.2.04.34.13.42.09.08.23.12.42.12h.16s.06.02.06.06v.26s-.02.06-.06.06c-.05,0-.13,0-.23,0-.3,0-.53-.06-.68-.17-.15-.11-.23-.32-.23-.62v-1.98s0-.03-.02-.03h-.38s-.06-.02-.06-.06v-.24s.02-.06.06-.06h.38s.02,0,.02-.03v-.72s.02-.06.06-.06h.31s.06.02.06.06v.72s0,.03.03.03h.71s.06.02.06.06v.24s-.02.06-.06.06Z" />
                    <path class="cls-18"
                        d="M333.29,163.02c.2.19.31.45.31.76v2.1s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-.26s0-.01,0-.02c0,0-.01,0-.02,0-.1.12-.24.22-.41.28-.17.06-.35.1-.56.1-.3,0-.54-.08-.74-.22-.2-.15-.3-.38-.3-.69s.11-.56.34-.74c.23-.18.54-.27.95-.27h.72s.03,0,.03-.03v-.24c0-.21-.06-.38-.18-.5-.12-.12-.3-.18-.55-.18-.2,0-.36.04-.49.12s-.2.18-.23.32c-.01.04-.04.06-.08.06l-.34-.04s-.07-.03-.06-.05c.03-.23.16-.42.38-.57.22-.15.49-.22.81-.22.38,0,.66.1.87.29ZM332.88,165.43c.18-.12.27-.29.27-.5v-.49s0-.03-.03-.03h-.66c-.27,0-.5.06-.66.17-.17.11-.25.27-.25.47,0,.18.06.32.19.42s.29.14.5.14c.25,0,.46-.06.64-.18Z" />
                    <path class="cls-18"
                        d="M338.46,163.02c.18.19.27.44.27.77v2.09s-.02.06-.06.06h-.31s-.06-.02-.06-.06v-2.01c0-.23-.07-.41-.2-.55-.13-.14-.3-.2-.52-.2s-.4.07-.53.2c-.14.13-.2.31-.2.54v2.02s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-2.01c0-.23-.06-.41-.19-.55-.13-.14-.3-.2-.52-.2s-.4.07-.53.2c-.13.13-.2.31-.2.54v2.02s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-3.04s.02-.06.06-.06h.32s.06.02.06.06v.25s0,.01,0,.02.01,0,.02,0c.09-.12.21-.21.35-.27s.3-.09.48-.09c.22,0,.4.04.56.13.15.09.27.21.35.38,0,.02.02.02.03,0,.09-.17.22-.3.39-.38.17-.08.36-.13.57-.13.31,0,.56.09.74.28Z" />
                    <path class="cls-18"
                        d="M340.07,165.76c-.22-.15-.37-.37-.45-.64-.06-.19-.09-.45-.09-.77s.03-.57.09-.76c.08-.27.23-.48.44-.63s.48-.23.79-.23.55.08.77.23c.21.15.36.36.44.62.06.18.09.44.09.77s-.03.59-.09.77c-.08.27-.23.48-.44.64-.21.15-.47.23-.77.23s-.56-.08-.77-.23ZM341.36,165.44c.14-.11.24-.26.29-.45.04-.15.06-.36.06-.63s-.02-.49-.05-.63c-.05-.19-.15-.34-.29-.45s-.32-.17-.52-.17-.38.06-.52.17c-.14.11-.24.26-.29.45-.03.15-.05.36-.05.63s.02.49.05.63c.05.19.14.34.28.45.14.11.32.17.53.17s.37-.05.51-.17Z" />
                    <path class="cls-18"
                        d="M343.46,165.86c-.18-.08-.32-.17-.42-.3-.1-.12-.15-.26-.15-.42v-.08s.02-.06.06-.06h.3s.06.02.06.06v.05c0,.14.07.26.21.36.14.1.32.15.54.15s.39-.05.53-.15c.13-.1.2-.22.2-.37,0-.1-.03-.19-.1-.26-.07-.07-.15-.12-.24-.16-.09-.04-.24-.08-.43-.14-.23-.07-.42-.13-.57-.2-.15-.07-.28-.16-.38-.28-.1-.12-.15-.27-.15-.45,0-.27.1-.48.31-.64.21-.16.48-.24.82-.24.23,0,.43.04.61.11.18.08.31.18.41.31.1.13.14.28.14.44v.02s-.02.06-.06.06h-.29s-.06-.02-.06-.06v-.02c0-.14-.07-.26-.2-.36s-.32-.14-.55-.14c-.21,0-.38.04-.51.13-.13.09-.19.2-.19.35,0,.14.06.25.19.32.12.08.32.15.58.23.24.07.44.14.59.2.15.06.28.15.39.27s.16.27.16.47c0,.27-.11.49-.32.65-.21.16-.49.24-.84.24-.23,0-.44-.04-.62-.11Z" />
                    <path class="cls-18"
                        d="M348.66,163.15h-.71s-.03,0-.03.03v1.84c0,.2.04.34.13.42.09.08.23.12.42.12h.16s.06.02.06.06v.26s-.02.06-.06.06c-.05,0-.13,0-.23,0-.3,0-.53-.06-.68-.17-.15-.11-.23-.32-.23-.62v-1.98s0-.03-.02-.03h-.38s-.06-.02-.06-.06v-.24s.02-.06.06-.06h.38s.02,0,.02-.03v-.72s.02-.06.06-.06h.31s.06.02.06.06v.72s0,.03.03.03h.71s.06.02.06.06v.24s-.02.06-.06.06Z" />
                    <path class="cls-18"
                        d="M351.21,162.83s.05.04.04.08l-.07.31s-.04.05-.08.04c-.07-.03-.15-.04-.24-.04h-.08c-.22.01-.4.09-.54.25s-.21.34-.21.58v1.84s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-3.04s.02-.06.06-.06h.32s.06.02.06.06v.38s0,.02,0,.02c0,0,.01,0,.02,0,.09-.15.2-.27.33-.36.14-.09.29-.13.47-.13.14,0,.26.03.36.08Z" />
                    <path class="cls-18"
                        d="M353.8,163.02c.2.19.31.45.31.76v2.1s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-.26s0-.01,0-.02c0,0-.01,0-.02,0-.1.12-.24.22-.41.28-.17.06-.35.1-.56.1-.3,0-.54-.08-.74-.22-.2-.15-.3-.38-.3-.69s.11-.56.34-.74c.23-.18.54-.27.95-.27h.72s.03,0,.03-.03v-.24c0-.21-.06-.38-.18-.5-.12-.12-.3-.18-.55-.18-.2,0-.36.04-.49.12s-.2.18-.23.32c-.01.04-.04.06-.08.06l-.34-.04s-.07-.03-.06-.05c.03-.23.16-.42.38-.57.22-.15.49-.22.81-.22.38,0,.66.1.87.29ZM353.4,165.43c.18-.12.27-.29.27-.5v-.49s0-.03-.03-.03h-.66c-.27,0-.5.06-.66.17-.17.11-.25.27-.25.47,0,.18.06.32.19.42s.29.14.5.14c.25,0,.46-.06.64-.18Z" />
                    <path class="cls-18"
                        d="M357.54,164.36c0,.34-.03.61-.09.79-.08.26-.22.46-.41.61-.2.15-.44.22-.73.22-.17,0-.32-.03-.46-.1-.14-.07-.25-.16-.34-.29,0,0,0-.01-.02,0,0,0-.01,0-.01.02v.27s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-4.25s.02-.06.06-.06h.32s.06.02.06.06v1.47s0,.01,0,.02c0,0,.01,0,.02,0,.09-.12.21-.21.34-.28.14-.07.29-.1.46-.1.29,0,.54.08.74.23s.34.36.41.63c.05.19.07.45.07.78ZM357.07,164.82c.02-.11.03-.26.03-.46s-.01-.36-.03-.48c-.02-.11-.06-.22-.11-.31-.05-.14-.14-.25-.26-.33-.12-.08-.26-.12-.43-.12-.16,0-.29.04-.41.13-.11.09-.2.2-.25.34-.04.09-.07.19-.09.3-.02.11-.03.27-.03.48s0,.36.03.47c.02.11.05.21.08.3.05.15.14.26.25.35s.25.13.42.13.33-.04.45-.13.22-.21.27-.36c.04-.09.07-.19.09-.3Z" />
                    <path class="cls-18"
                        d="M360.41,163.02c.2.19.31.45.31.76v2.1s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-.26s0-.01,0-.02c0,0-.01,0-.02,0-.1.12-.24.22-.41.28-.17.06-.35.1-.56.1-.3,0-.54-.08-.74-.22-.2-.15-.3-.38-.3-.69s.11-.56.34-.74c.23-.18.54-.27.95-.27h.72s.03,0,.03-.03v-.24c0-.21-.06-.38-.18-.5-.12-.12-.3-.18-.55-.18-.2,0-.36.04-.49.12s-.2.18-.23.32c-.01.04-.04.06-.08.06l-.34-.04s-.07-.03-.06-.05c.03-.23.16-.42.38-.57.22-.15.49-.22.81-.22.38,0,.66.1.87.29ZM360,165.43c.18-.12.27-.29.27-.5v-.49s0-.03-.03-.03h-.66c-.27,0-.5.06-.66.17-.17.11-.25.27-.25.47,0,.18.06.32.19.42s.29.14.5.14c.25,0,.46-.06.64-.18Z" />
                    <path class="cls-18"
                        d="M360.92,167.18v-.26s.02-.06.06-.06c.45,0,.67-.22.67-.64v-3.38s.02-.06.06-.06h.32s.06.02.06.06v3.38c0,.35-.09.61-.26.77-.17.16-.46.25-.85.25-.04,0-.06-.02-.06-.06ZM361.62,162.11c-.06-.07-.1-.15-.1-.25s.03-.19.1-.25.15-.1.25-.1.18.03.25.1.1.15.1.25-.03.18-.1.25c-.07.07-.15.1-.25.1s-.19-.03-.25-.1Z" />
                    <path class="cls-18"
                        d="M365.13,163.02c.2.19.31.45.31.76v2.1s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-.26s0-.01,0-.02c0,0-.01,0-.02,0-.1.12-.24.22-.41.28-.17.06-.35.1-.56.1-.3,0-.54-.08-.74-.22-.2-.15-.3-.38-.3-.69s.11-.56.34-.74c.23-.18.54-.27.95-.27h.72s.03,0,.03-.03v-.24c0-.21-.06-.38-.18-.5-.12-.12-.3-.18-.55-.18-.2,0-.36.04-.49.12s-.2.18-.23.32c-.01.04-.04.06-.08.06l-.34-.04s-.07-.03-.06-.05c.03-.23.16-.42.38-.57.22-.15.49-.22.81-.22.38,0,.66.1.87.29ZM364.73,165.43c.18-.12.27-.29.27-.5v-.49s0-.03-.03-.03h-.66c-.27,0-.5.06-.66.17-.17.11-.25.27-.25.47,0,.18.06.32.19.42s.29.14.5.14c.25,0,.46-.06.64-.18Z" />
                    <path class="cls-18"
                        d="M368.47,163.03c.2.2.3.46.3.79v2.06s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-1.97c0-.23-.07-.42-.21-.56-.14-.15-.32-.22-.54-.22s-.42.07-.56.21-.21.33-.21.56v1.99s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-3.04s.02-.06.06-.06h.32s.06.02.06.06v.27s0,.01,0,.02.01,0,.02,0c.18-.26.46-.39.84-.39.33,0,.6.1.8.29Z" />
                    <path class="cls-18"
                        d="M371.71,161.57h.32s.06.02.06.06v4.25s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-.26s0-.01,0-.02-.01,0-.02,0c-.09.12-.21.21-.34.28-.14.07-.29.1-.45.1-.29,0-.54-.08-.73-.22-.2-.15-.33-.35-.41-.61-.06-.19-.09-.45-.09-.79s.03-.6.08-.78c.08-.27.21-.48.41-.63s.45-.23.74-.23c.16,0,.31.03.45.1.14.06.25.16.34.28,0,0,.02.01.02,0s0,0,0-.02v-1.47s.02-.06.06-.06ZM371.62,164.83c.02-.11.02-.27.02-.47s0-.36-.02-.47c-.02-.12-.05-.22-.09-.3-.05-.14-.13-.25-.25-.34-.11-.08-.25-.13-.41-.13-.17,0-.31.04-.43.12-.12.08-.21.19-.26.33-.05.09-.09.2-.11.31-.02.12-.04.27-.04.48s0,.35.03.46.05.21.09.3c.05.15.14.27.27.36.13.09.28.13.45.13.17,0,.31-.04.42-.13.11-.09.2-.2.25-.35.04-.08.06-.18.08-.29Z" />
                    <path class="cls-18"
                        d="M373.53,165.76c-.22-.15-.37-.37-.45-.64-.06-.19-.09-.45-.09-.77s.03-.57.09-.76c.08-.27.23-.48.44-.63s.48-.23.79-.23.55.08.77.23c.21.15.36.36.44.62.06.18.09.44.09.77s-.03.59-.09.77c-.08.27-.23.48-.44.64-.21.15-.47.23-.77.23s-.56-.08-.77-.23ZM374.82,165.44c.14-.11.24-.26.29-.45.04-.15.06-.36.06-.63s-.02-.49-.05-.63c-.05-.19-.15-.34-.29-.45s-.32-.17-.52-.17-.38.06-.52.17c-.14.11-.24.26-.29.45-.03.15-.05.36-.05.63s.02.49.05.63c.05.19.14.34.28.45.14.11.32.17.53.17s.37-.05.51-.17Z" />
                    <path class="cls-18"
                        d="M380.31,164.36c0,.33-.03.59-.08.77-.08.27-.21.48-.41.63-.2.15-.45.23-.75.23-.16,0-.31-.03-.45-.1s-.25-.16-.34-.28c0,0-.02-.01-.02,0,0,0,0,0,0,.02v1.47s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-4.25s.02-.06.06-.06h.32s.06.02.06.06v.26s0,.01,0,.02c0,0,.01,0,.02,0,.09-.12.21-.21.34-.28s.29-.1.45-.1c.29,0,.54.08.73.23.2.15.33.35.41.61.06.2.09.46.09.79ZM379.82,164.84c.02-.11.03-.27.03-.48s0-.35-.02-.46c-.02-.11-.05-.21-.09-.3-.05-.15-.14-.27-.27-.36-.13-.09-.28-.13-.45-.13s-.31.04-.42.13c-.11.09-.2.2-.25.35-.04.08-.06.18-.08.29s-.02.27-.02.47,0,.36.02.47.05.22.09.3c.05.14.13.26.25.34.11.09.25.13.41.13.17,0,.31-.04.43-.12.12-.08.21-.19.27-.33.04-.09.08-.19.1-.31Z" />
                    <path class="cls-18"
                        d="M383.17,163.02c.2.19.31.45.31.76v2.1s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-.26s0-.01,0-.02c0,0-.01,0-.02,0-.1.12-.24.22-.41.28-.17.06-.35.1-.56.1-.3,0-.54-.08-.74-.22-.2-.15-.3-.38-.3-.69s.11-.56.34-.74c.23-.18.54-.27.95-.27h.72s.03,0,.03-.03v-.24c0-.21-.06-.38-.18-.5-.12-.12-.3-.18-.55-.18-.2,0-.36.04-.49.12s-.2.18-.23.32c-.01.04-.04.06-.08.06l-.34-.04s-.07-.03-.06-.05c.03-.23.16-.42.38-.57.22-.15.49-.22.81-.22.38,0,.66.1.87.29ZM382.76,165.43c.18-.12.27-.29.27-.5v-.49s0-.03-.03-.03h-.66c-.27,0-.5.06-.66.17-.17.11-.25.27-.25.47,0,.18.06.32.19.42s.29.14.5.14c.25,0,.46-.06.64-.18Z" />
                    <path class="cls-18"
                        d="M386.03,162.83s.05.04.04.08l-.07.31s-.04.05-.08.04c-.07-.03-.15-.04-.24-.04h-.08c-.22.01-.4.09-.54.25s-.21.34-.21.58v1.84s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-3.04s.02-.06.06-.06h.32s.06.02.06.06v.38s0,.02,0,.02c0,0,.01,0,.02,0,.09-.15.2-.27.33-.36.14-.09.29-.13.47-.13.14,0,.26.03.36.08Z" />
                    <path class="cls-18"
                        d="M388.63,163.02c.2.19.31.45.31.76v2.1s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-.26s0-.01,0-.02c0,0-.01,0-.02,0-.1.12-.24.22-.41.28-.17.06-.35.1-.56.1-.3,0-.54-.08-.74-.22-.2-.15-.3-.38-.3-.69s.11-.56.34-.74c.23-.18.54-.27.95-.27h.72s.03,0,.03-.03v-.24c0-.21-.06-.38-.18-.5-.12-.12-.3-.18-.55-.18-.2,0-.36.04-.49.12s-.2.18-.23.32c-.01.04-.04.06-.08.06l-.34-.04s-.07-.03-.06-.05c.03-.23.16-.42.38-.57.22-.15.49-.22.81-.22.38,0,.66.1.87.29ZM388.22,165.43c.18-.12.27-.29.27-.5v-.49s0-.03-.03-.03h-.66c-.27,0-.5.06-.66.17-.17.11-.25.27-.25.47,0,.18.06.32.19.42s.29.14.5.14c.25,0,.46-.06.64-.18Z" />
                    <path class="cls-18"
                        d="M391.54,165.76c-.22-.15-.37-.37-.45-.64-.06-.19-.09-.45-.09-.77s.03-.57.09-.76c.08-.27.23-.48.44-.63s.48-.23.79-.23.55.08.77.23c.21.15.36.36.44.62.06.18.09.44.09.77s-.03.59-.09.77c-.08.27-.23.48-.44.64-.21.15-.47.23-.77.23s-.56-.08-.77-.23ZM392.83,165.44c.14-.11.24-.26.29-.45.04-.15.06-.36.06-.63s-.02-.49-.05-.63c-.05-.19-.15-.34-.29-.45s-.32-.17-.52-.17-.38.06-.52.17c-.14.11-.24.26-.29.45-.03.15-.05.36-.05.63s.02.49.05.63c.05.19.14.34.28.45.14.11.32.17.53.17s.37-.05.51-.17Z" />
                    <path class="cls-18"
                        d="M395.26,162.11c-.08.1-.12.28-.12.52v.12s0,.03.03.03h.7s.06.02.06.06v.28s-.02.06-.06.06h-.7s-.03,0-.03.02v2.67s-.02.06-.06.06h-.31s-.06-.02-.06-.06v-2.67s0-.02-.03-.02h-.42s-.06-.02-.06-.06v-.28s.02-.06.06-.06h.42s.03,0,.03-.03v-.15c0-.26.03-.46.09-.61.06-.15.16-.26.3-.32.14-.07.34-.1.6-.1h.18s.06.02.06.06v.26s-.02.06-.06.06h-.14c-.23,0-.39.05-.47.15Z" />
                    <path class="cls-18"
                        d="M398.33,162.83s.05.04.04.08l-.07.31s-.04.05-.08.04c-.07-.03-.15-.04-.24-.04h-.08c-.22.01-.4.09-.54.25s-.21.34-.21.58v1.84s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-3.04s.02-.06.06-.06h.32s.06.02.06.06v.38s0,.02,0,.02c0,0,.01,0,.02,0,.09-.15.2-.27.33-.36.14-.09.29-.13.47-.13.14,0,.26.03.36.08Z" />
                    <path class="cls-18"
                        d="M401.39,164.27v.2s-.02.06-.06.06h-2.02s-.03,0-.03.02c0,.23.02.38.04.44.05.19.14.33.29.44.15.11.34.16.58.16.17,0,.33-.04.46-.12.13-.08.24-.19.31-.34.02-.04.05-.05.09-.02l.24.14s.04.05.02.09c-.1.2-.26.36-.47.47-.21.11-.45.17-.72.17-.3,0-.54-.08-.74-.21s-.34-.33-.43-.58c-.08-.2-.11-.49-.11-.86,0-.17,0-.32,0-.43,0-.11.02-.21.05-.3.07-.27.21-.49.43-.65s.47-.24.78-.24c.38,0,.67.1.88.29s.33.46.38.81c.02.12.03.26.03.44ZM399.6,163.28c-.14.11-.22.25-.27.43-.02.09-.04.23-.05.42,0,.02,0,.03.03.03h1.62s.03,0,.03-.03c0-.19-.02-.32-.04-.4-.05-.19-.14-.34-.28-.45-.14-.11-.32-.17-.53-.17s-.37.05-.51.16Z" />
                    <path class="cls-18"
                        d="M402.7,165.76c-.21-.16-.36-.37-.44-.63-.05-.18-.08-.44-.08-.78,0-.3.03-.55.08-.76.08-.26.22-.47.44-.62s.47-.23.77-.23.57.08.79.23.37.35.43.58c.02.08.03.14.04.19h0s-.02.06-.06.07l-.31.04h-.01s-.05-.02-.06-.06l-.02-.09c-.04-.16-.13-.29-.28-.4-.15-.11-.32-.17-.53-.17s-.38.06-.52.17-.23.26-.27.44c-.04.16-.06.37-.06.63,0,.28.02.49.06.64.04.19.13.34.27.45s.31.17.52.17.38-.05.53-.16c.15-.11.24-.24.28-.41v-.05s.04-.06.08-.05l.31.05s.06.03.06.07l-.02.12c-.06.24-.21.44-.43.59-.22.15-.49.22-.79.22s-.56-.08-.77-.23Z" />
                    <path class="cls-18"
                        d="M407.96,164.27v.2s-.02.06-.06.06h-2.02s-.03,0-.03.02c0,.23.02.38.04.44.05.19.14.33.29.44.15.11.34.16.58.16.17,0,.33-.04.46-.12.13-.08.24-.19.31-.34.02-.04.05-.05.09-.02l.24.14s.04.05.02.09c-.1.2-.26.36-.47.47-.21.11-.45.17-.72.17-.3,0-.54-.08-.74-.21s-.34-.33-.43-.58c-.08-.2-.11-.49-.11-.86,0-.17,0-.32,0-.43,0-.11.02-.21.05-.3.07-.27.21-.49.43-.65s.47-.24.78-.24c.38,0,.67.1.88.29s.33.46.38.81c.02.12.03.26.03.44ZM406.17,163.28c-.14.11-.22.25-.27.43-.02.09-.04.23-.05.42,0,.02,0,.03.03.03h1.62s.03,0,.03-.03c0-.19-.02-.32-.04-.4-.05-.19-.14-.34-.28-.45-.14-.11-.32-.17-.53-.17s-.37.05-.51.16Z" />
                    <path class="cls-18"
                        d="M410.47,162.83s.05.04.04.08l-.07.31s-.04.05-.08.04c-.07-.03-.15-.04-.24-.04h-.08c-.22.01-.4.09-.54.25s-.21.34-.21.58v1.84s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-3.04s.02-.06.06-.06h.32s.06.02.06.06v.38s0,.02,0,.02c0,0,.01,0,.02,0,.09-.15.2-.27.33-.36.14-.09.29-.13.47-.13.14,0,.26.03.36.08Z" />
                    <path class="cls-18"
                        d="M412.56,163.15h-.71s-.03,0-.03.03v1.84c0,.2.04.34.13.42.09.08.23.12.42.12h.16s.06.02.06.06v.26s-.02.06-.06.06c-.05,0-.13,0-.23,0-.3,0-.53-.06-.68-.17-.15-.11-.23-.32-.23-.62v-1.98s0-.03-.02-.03h-.38s-.06-.02-.06-.06v-.24s.02-.06.06-.06h.38s.02,0,.02-.03v-.72s.02-.06.06-.06h.31s.06.02.06.06v.72s0,.03.03.03h.71s.06.02.06.06v.24s-.02.06-.06.06Z" />
                    <path class="cls-18"
                        d="M415.83,164.27v.2s-.02.06-.06.06h-2.02s-.03,0-.03.02c0,.23.02.38.04.44.05.19.14.33.29.44.15.11.34.16.58.16.17,0,.33-.04.46-.12.13-.08.24-.19.31-.34.02-.04.05-.05.09-.02l.24.14s.04.05.02.09c-.1.2-.26.36-.47.47-.21.11-.45.17-.72.17-.3,0-.54-.08-.74-.21s-.34-.33-.43-.58c-.08-.2-.11-.49-.11-.86,0-.17,0-.32,0-.43,0-.11.02-.21.05-.3.07-.27.21-.49.43-.65s.47-.24.78-.24c.38,0,.67.1.88.29s.33.46.38.81c.02.12.03.26.03.44ZM414.04,163.28c-.14.11-.22.25-.27.43-.02.09-.04.23-.05.42,0,.02,0,.03.03.03h1.62s.03,0,.03-.03c0-.19-.02-.32-.04-.4-.05-.19-.14-.34-.28-.45-.14-.11-.32-.17-.53-.17s-.37.05-.51.16Z" />
                    <path class="cls-18"
                        d="M419.94,162.78h.32s.06.02.06.06v3.04s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-.27s0-.01-.01-.02c0,0-.01,0-.02,0-.17.26-.45.39-.83.39-.2,0-.38-.04-.55-.12s-.3-.2-.4-.35c-.1-.15-.15-.34-.15-.56v-2.11s.02-.06.06-.06h.32s.06.02.06.06v1.98c0,.24.07.43.2.57.13.14.31.21.54.21s.43-.07.57-.21c.14-.14.21-.33.21-.56v-1.98s.02-.06.06-.06Z" />
                    <path class="cls-18"
                        d="M423.42,163.03c.2.2.3.46.3.79v2.06s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-1.97c0-.23-.07-.42-.21-.56-.14-.15-.32-.22-.54-.22s-.42.07-.56.21-.21.33-.21.56v1.99s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-3.04s.02-.06.06-.06h.32s.06.02.06.06v.27s0,.01,0,.02.01,0,.02,0c.18-.26.46-.39.84-.39.33,0,.6.1.8.29Z" />
                    <path class="cls-18"
                        d="M426.65,163.02c.2.19.31.45.31.76v2.1s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-.26s0-.01,0-.02c0,0-.01,0-.02,0-.1.12-.24.22-.41.28-.17.06-.35.1-.56.1-.3,0-.54-.08-.74-.22-.2-.15-.3-.38-.3-.69s.11-.56.34-.74c.23-.18.54-.27.95-.27h.72s.03,0,.03-.03v-.24c0-.21-.06-.38-.18-.5-.12-.12-.3-.18-.55-.18-.2,0-.36.04-.49.12s-.2.18-.23.32c-.01.04-.04.06-.08.06l-.34-.04s-.07-.03-.06-.05c.03-.23.16-.42.38-.57.22-.15.49-.22.81-.22.38,0,.66.1.87.29ZM426.24,165.43c.18-.12.27-.29.27-.5v-.49s0-.03-.03-.03h-.66c-.27,0-.5.06-.66.17-.17.11-.25.27-.25.47,0,.18.06.32.19.42s.29.14.5.14c.25,0,.46-.06.64-.18Z" />
                    <path class="cls-18"
                        d="M334.46,171.15c.18.19.27.44.27.77v2.09s-.02.06-.06.06h-.31s-.06-.02-.06-.06v-2.01c0-.23-.07-.41-.2-.55-.13-.14-.3-.2-.52-.2s-.4.07-.53.2c-.14.13-.2.31-.2.54v2.02s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-2.01c0-.23-.06-.41-.19-.55-.13-.14-.3-.2-.52-.2s-.4.07-.53.2c-.13.13-.2.31-.2.54v2.02s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-3.04s.02-.06.06-.06h.32s.06.02.06.06v.25s0,.01,0,.02.01,0,.02,0c.09-.12.21-.21.35-.27s.3-.09.48-.09c.22,0,.4.04.56.13.15.09.27.21.35.38,0,.02.02.02.03,0,.09-.17.22-.3.39-.38.17-.08.36-.13.57-.13.31,0,.56.09.74.28Z" />
                    <path class="cls-18"
                        d="M338.1,172.4v.2s-.02.06-.06.06h-2.02s-.03,0-.03.02c0,.23.02.38.04.44.05.19.14.33.29.44.15.11.34.16.58.16.17,0,.33-.04.46-.12.13-.08.24-.19.31-.34.02-.04.05-.05.09-.02l.24.14s.04.05.02.09c-.1.2-.26.36-.47.47-.21.11-.45.17-.72.17-.3,0-.54-.08-.74-.21s-.34-.33-.43-.58c-.08-.2-.11-.49-.11-.86,0-.17,0-.32,0-.43,0-.11.02-.21.05-.3.07-.27.21-.49.43-.65s.47-.24.78-.24c.38,0,.67.1.88.29s.33.46.38.81c.02.12.03.26.03.44ZM336.31,171.4c-.14.11-.22.25-.27.43-.02.09-.04.23-.05.42,0,.02,0,.03.03.03h1.62s.03,0,.03-.03c0-.19-.02-.32-.04-.4-.05-.19-.14-.34-.28-.45-.14-.11-.32-.17-.53-.17s-.37.05-.51.16Z" />
                    <path class="cls-18"
                        d="M338.26,175.31v-.26s.02-.06.06-.06c.45,0,.67-.22.67-.64v-3.38s.02-.06.06-.06h.32s.06.02.06.06v3.38c0,.35-.09.61-.26.77-.17.16-.46.25-.85.25-.04,0-.06-.02-.06-.06ZM338.96,170.23c-.06-.07-.1-.15-.1-.25s.03-.19.1-.25.15-.1.25-.1.18.03.25.1.1.15.1.25-.03.18-.1.25c-.07.07-.15.1-.25.1s-.19-.03-.25-.1Z" />
                    <path class="cls-18"
                        d="M340.94,173.89c-.22-.15-.37-.37-.45-.64-.06-.19-.09-.45-.09-.77s.03-.57.09-.76c.08-.27.23-.48.44-.63s.48-.23.79-.23.55.08.77.23c.21.15.36.36.44.62.06.18.09.44.09.77s-.03.59-.09.77c-.08.27-.23.48-.44.64-.21.15-.47.23-.77.23s-.56-.08-.77-.23ZM342.23,173.57c.14-.11.24-.26.29-.45.04-.15.06-.36.06-.63s-.02-.49-.05-.63c-.05-.19-.15-.34-.29-.45s-.32-.17-.52-.17-.38.06-.52.17c-.14.11-.24.26-.29.45-.03.15-.05.36-.05.63s.02.49.05.63c.05.19.14.34.28.45.14.11.32.17.53.17s.37-.05.51-.17Z" />
                    <path class="cls-18"
                        d="M345.55,170.96s.05.04.04.08l-.07.31s-.04.05-.08.04c-.07-.03-.15-.04-.24-.04h-.08c-.22.01-.4.09-.54.25s-.21.34-.21.58v1.84s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-3.04s.02-.06.06-.06h.32s.06.02.06.06v.38s0,.02,0,.02c0,0,.01,0,.02,0,.09-.15.2-.27.33-.36.14-.09.29-.13.47-.13.14,0,.26.03.36.08Z" />
                    <path class="cls-18"
                        d="M349.89,172.4v.2s-.02.06-.06.06h-2.02s-.03,0-.03.02c0,.23.02.38.04.44.05.19.14.33.29.44.15.11.34.16.58.16.17,0,.33-.04.46-.12.13-.08.24-.19.31-.34.02-.04.05-.05.09-.02l.24.14s.04.05.02.09c-.1.2-.26.36-.47.47-.21.11-.45.17-.72.17-.3,0-.54-.08-.74-.21s-.34-.33-.43-.58c-.08-.2-.11-.49-.11-.86,0-.17,0-.32,0-.43,0-.11.02-.21.05-.3.07-.27.21-.49.43-.65s.47-.24.78-.24c.38,0,.67.1.88.29s.33.46.38.81c.02.12.03.26.03.44ZM348.1,171.4c-.14.11-.22.25-.27.43-.02.09-.04.23-.05.42,0,.02,0,.03.03.03h1.62s.03,0,.03-.03c0-.19-.02-.32-.04-.4-.05-.19-.14-.34-.28-.45-.14-.11-.32-.17-.53-.17s-.37.05-.51.16Z" />
                    <path class="cls-18"
                        d="M350.36,174.05s0-.03,0-.05l1.04-1.49s0-.03,0-.04l-1.04-1.49s-.01-.02-.01-.03c0-.03.02-.04.06-.04h.34s.06.01.08.04l.82,1.18s0,0,.02,0c0,0,.01,0,.02,0l.81-1.18s.04-.04.08-.04h.37s.04,0,.05.02c0,.01,0,.03-.01.05l-1.04,1.49s0,.02,0,.04l1.04,1.49s.01.02.01.04c0,0,0,.02-.01.02,0,0-.02.01-.04.01h-.34s-.06-.01-.07-.04l-.81-1.18s-.01-.01-.02-.01c0,0-.01,0-.02.01l-.83,1.18s-.04.04-.07.04h-.36s-.04,0-.05-.03Z" />
                    <path class="cls-18"
                        d="M356.22,172.49c0,.33-.03.59-.08.77-.08.27-.21.48-.41.63-.2.15-.45.23-.75.23-.16,0-.31-.03-.45-.1s-.25-.16-.34-.28c0,0-.02-.01-.02,0,0,0,0,0,0,.02v1.47s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-4.25s.02-.06.06-.06h.32s.06.02.06.06v.26s0,.01,0,.02c0,0,.01,0,.02,0,.09-.12.21-.21.34-.28s.29-.1.45-.1c.29,0,.54.08.73.23.2.15.33.35.41.61.06.2.09.46.09.79ZM355.73,172.97c.02-.11.03-.27.03-.48s0-.35-.02-.46c-.02-.11-.05-.21-.09-.3-.05-.15-.14-.27-.27-.36-.13-.09-.28-.13-.45-.13s-.31.04-.42.13c-.11.09-.2.2-.25.35-.04.08-.06.18-.08.29s-.02.27-.02.47,0,.36.02.47.05.22.09.3c.05.14.13.26.25.34.11.09.25.13.41.13.17,0,.31-.04.43-.12.12-.08.21-.19.27-.33.04-.09.08-.19.1-.31Z" />
                    <path class="cls-18"
                        d="M359.52,172.4v.2s-.02.06-.06.06h-2.02s-.03,0-.03.02c0,.23.02.38.04.44.05.19.14.33.29.44.15.11.34.16.58.16.17,0,.33-.04.46-.12.13-.08.24-.19.31-.34.02-.04.05-.05.09-.02l.24.14s.04.05.02.09c-.1.2-.26.36-.47.47-.21.11-.45.17-.72.17-.3,0-.54-.08-.74-.21s-.34-.33-.43-.58c-.08-.2-.11-.49-.11-.86,0-.17,0-.32,0-.43,0-.11.02-.21.05-.3.07-.27.21-.49.43-.65s.47-.24.78-.24c.38,0,.67.1.88.29s.33.46.38.81c.02.12.03.26.03.44ZM357.73,171.4c-.14.11-.22.25-.27.43-.02.09-.04.23-.05.42,0,.02,0,.03.03.03h1.62s.03,0,.03-.03c0-.19-.02-.32-.04-.4-.05-.19-.14-.34-.28-.45-.14-.11-.32-.17-.53-.17s-.37.05-.51.16Z" />
                    <path class="cls-18"
                        d="M362.04,170.96s.05.04.04.08l-.07.31s-.04.05-.08.04c-.07-.03-.15-.04-.24-.04h-.08c-.22.01-.4.09-.54.25s-.21.34-.21.58v1.84s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-3.04s.02-.06.06-.06h.32s.06.02.06.06v.38s0,.02,0,.02c0,0,.01,0,.02,0,.09-.15.2-.27.33-.36.14-.09.29-.13.47-.13.14,0,.26.03.36.08Z" />
                    <path class="cls-18"
                        d="M362.73,170.23c-.07-.07-.1-.15-.1-.25s.03-.19.1-.25.15-.1.25-.1.18.03.25.1.1.15.1.25-.03.18-.1.25c-.07.07-.15.1-.25.1s-.18-.03-.25-.1ZM362.74,174v-3.04s.02-.06.06-.06h.32s.06.02.06.06v3.04s-.02.06-.06.06h-.32s-.06-.02-.06-.06Z" />
                    <path class="cls-18"
                        d="M366.72,172.4v.2s-.02.06-.06.06h-2.02s-.03,0-.03.02c0,.23.02.38.04.44.05.19.14.33.29.44.15.11.34.16.58.16.17,0,.33-.04.46-.12.13-.08.24-.19.31-.34.02-.04.05-.05.09-.02l.24.14s.04.05.02.09c-.1.2-.26.36-.47.47-.21.11-.45.17-.72.17-.3,0-.54-.08-.74-.21s-.34-.33-.43-.58c-.08-.2-.11-.49-.11-.86,0-.17,0-.32,0-.43,0-.11.02-.21.05-.3.07-.27.21-.49.43-.65s.47-.24.78-.24c.38,0,.67.1.88.29s.33.46.38.81c.02.12.03.26.03.44ZM364.93,171.4c-.14.11-.22.25-.27.43-.02.09-.04.23-.05.42,0,.02,0,.03.03.03h1.62s.03,0,.03-.03c0-.19-.02-.32-.04-.4-.05-.19-.14-.34-.28-.45-.14-.11-.32-.17-.53-.17s-.37.05-.51.16Z" />
                    <path class="cls-18"
                        d="M369.71,171.16c.2.2.3.46.3.79v2.06s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-1.97c0-.23-.07-.42-.21-.56-.14-.15-.32-.22-.54-.22s-.42.07-.56.21-.21.33-.21.56v1.99s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-3.04s.02-.06.06-.06h.32s.06.02.06.06v.27s0,.01,0,.02.01,0,.02,0c.18-.26.46-.39.84-.39.33,0,.6.1.8.29Z" />
                    <path class="cls-18"
                        d="M371.35,173.89c-.21-.16-.36-.37-.44-.63-.05-.18-.08-.44-.08-.78,0-.3.03-.55.08-.76.08-.26.22-.47.44-.62s.47-.23.77-.23.57.08.79.23.37.35.43.58c.02.08.03.14.04.19h0s-.02.06-.06.07l-.31.04h-.01s-.05-.02-.06-.06l-.02-.09c-.04-.16-.13-.29-.28-.4-.15-.11-.32-.17-.53-.17s-.38.06-.52.17-.23.26-.27.44c-.04.16-.06.37-.06.63,0,.28.02.49.06.64.04.19.13.34.27.45s.31.17.52.17.38-.05.53-.16c.15-.11.24-.24.28-.41v-.05s.04-.06.08-.05l.31.05s.06.03.06.07l-.02.12c-.06.24-.21.44-.43.59-.22.15-.49.22-.79.22s-.56-.08-.77-.23Z" />
                    <path class="cls-18"
                        d="M374.23,170.23c-.07-.07-.1-.15-.1-.25s.03-.19.1-.25.15-.1.25-.1.18.03.25.1.1.15.1.25-.03.18-.1.25c-.07.07-.15.1-.25.1s-.18-.03-.25-.1ZM374.24,174v-3.04s.02-.06.06-.06h.32s.06.02.06.06v3.04s-.02.06-.06.06h-.32s-.06-.02-.06-.06Z" />
                    <path class="cls-18"
                        d="M377.74,171.15c.2.19.31.45.31.76v2.1s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-.26s0-.01,0-.02c0,0-.01,0-.02,0-.1.12-.24.22-.41.28-.17.06-.35.1-.56.1-.3,0-.54-.08-.74-.22-.2-.15-.3-.38-.3-.69s.11-.56.34-.74c.23-.18.54-.27.95-.27h.72s.03,0,.03-.03v-.24c0-.21-.06-.38-.18-.5-.12-.12-.3-.18-.55-.18-.2,0-.36.04-.49.12s-.2.18-.23.32c-.01.04-.04.06-.08.06l-.34-.04s-.07-.03-.06-.05c.03-.23.16-.42.38-.57.22-.15.49-.22.81-.22.38,0,.66.1.87.29ZM377.33,173.56c.18-.12.27-.29.27-.5v-.49s0-.03-.03-.03h-.66c-.27,0-.5.06-.66.17-.17.11-.25.27-.25.47,0,.18.06.32.19.42s.29.14.5.14c.25,0,.46-.06.64-.18Z" />
                    <path class="cls-18"
                        d="M382.67,172.4v.2s-.02.06-.06.06h-2.02s-.03,0-.03.02c0,.23.02.38.04.44.05.19.14.33.29.44.15.11.34.16.58.16.17,0,.33-.04.46-.12.13-.08.24-.19.31-.34.02-.04.05-.05.09-.02l.24.14s.04.05.02.09c-.1.2-.26.36-.47.47-.21.11-.45.17-.72.17-.3,0-.54-.08-.74-.21s-.34-.33-.43-.58c-.08-.2-.11-.49-.11-.86,0-.17,0-.32,0-.43,0-.11.02-.21.05-.3.07-.27.21-.49.43-.65s.47-.24.78-.24c.38,0,.67.1.88.29s.33.46.38.81c.02.12.03.26.03.44ZM380.88,171.4c-.14.11-.22.25-.27.43-.02.09-.04.23-.05.42,0,.02,0,.03.03.03h1.62s.03,0,.03-.03c0-.19-.02-.32-.04-.4-.05-.19-.14-.34-.28-.45-.14-.11-.32-.17-.53-.17s-.37.05-.51.16Z" />
                    <path class="cls-18"
                        d="M385.66,171.16c.2.2.3.46.3.79v2.06s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-1.97c0-.23-.07-.42-.21-.56-.14-.15-.32-.22-.54-.22s-.42.07-.56.21-.21.33-.21.56v1.99s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-3.04s.02-.06.06-.06h.32s.06.02.06.06v.27s0,.01,0,.02.01,0,.02,0c.18-.26.46-.39.84-.39.33,0,.6.1.8.29Z" />
                    <path class="cls-18"
                        d="M390.72,172.49c0,.33-.03.59-.08.77-.08.27-.21.48-.41.63-.2.15-.45.23-.75.23-.16,0-.31-.03-.45-.1s-.25-.16-.34-.28c0,0-.02-.01-.02,0,0,0,0,0,0,.02v1.47s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-4.25s.02-.06.06-.06h.32s.06.02.06.06v.26s0,.01,0,.02c0,0,.01,0,.02,0,.09-.12.21-.21.34-.28s.29-.1.45-.1c.29,0,.54.08.73.23.2.15.33.35.41.61.06.2.09.46.09.79ZM390.23,172.97c.02-.11.03-.27.03-.48s0-.35-.02-.46c-.02-.11-.05-.21-.09-.3-.05-.15-.14-.27-.27-.36-.13-.09-.28-.13-.45-.13s-.31.04-.42.13c-.11.09-.2.2-.25.35-.04.08-.06.18-.08.29s-.02.27-.02.47,0,.36.02.47.05.22.09.3c.05.14.13.26.25.34.11.09.25.13.41.13.17,0,.31-.04.43-.12.12-.08.21-.19.27-.33.04-.09.08-.19.1-.31Z" />
                    <path class="cls-18"
                        d="M392,173.89c-.21-.16-.36-.37-.44-.63-.05-.18-.08-.44-.08-.78,0-.3.03-.55.08-.76.08-.26.22-.47.44-.62s.47-.23.77-.23.57.08.79.23.37.35.43.58c.02.08.03.14.04.19h0s-.02.06-.06.07l-.31.04h-.01s-.05-.02-.06-.06l-.02-.09c-.04-.16-.13-.29-.28-.4-.15-.11-.32-.17-.53-.17s-.38.06-.52.17-.23.26-.27.44c-.04.16-.06.37-.06.63,0,.28.02.49.06.64.04.19.13.34.27.45s.31.17.52.17.38-.05.53-.16c.15-.11.24-.24.28-.41v-.05s.04-.06.08-.05l.31.05s.06.03.06.07l-.02.12c-.06.24-.21.44-.43.59-.22.15-.49.22-.79.22s-.56-.08-.77-.23Z" />
                    <path class="cls-18"
                        d="M398.76,171.15c.18.19.27.44.27.77v2.09s-.02.06-.06.06h-.31s-.06-.02-.06-.06v-2.01c0-.23-.07-.41-.2-.55-.13-.14-.3-.2-.52-.2s-.4.07-.53.2c-.14.13-.2.31-.2.54v2.02s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-2.01c0-.23-.06-.41-.19-.55-.13-.14-.3-.2-.52-.2s-.4.07-.53.2c-.13.13-.2.31-.2.54v2.02s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-3.04s.02-.06.06-.06h.32s.06.02.06.06v.25s0,.01,0,.02.01,0,.02,0c.09-.12.21-.21.35-.27s.3-.09.48-.09c.22,0,.4.04.56.13.15.09.27.21.35.38,0,.02.02.02.03,0,.09-.17.22-.3.39-.38.17-.08.36-.13.57-.13.31,0,.56.09.74.28Z" />
                    <path class="cls-18"
                        d="M401.96,171.15c.2.19.31.45.31.76v2.1s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-.26s0-.01,0-.02c0,0-.01,0-.02,0-.1.12-.24.22-.41.28-.17.06-.35.1-.56.1-.3,0-.54-.08-.74-.22-.2-.15-.3-.38-.3-.69s.11-.56.34-.74c.23-.18.54-.27.95-.27h.72s.03,0,.03-.03v-.24c0-.21-.06-.38-.18-.5-.12-.12-.3-.18-.55-.18-.2,0-.36.04-.49.12s-.2.18-.23.32c-.01.04-.04.06-.08.06l-.34-.04s-.07-.03-.06-.05c.03-.23.16-.42.38-.57.22-.15.49-.22.81-.22.38,0,.66.1.87.29ZM401.55,173.56c.18-.12.27-.29.27-.5v-.49s0-.03-.03-.03h-.66c-.27,0-.5.06-.66.17-.17.11-.25.27-.25.47,0,.18.06.32.19.42s.29.14.5.14c.25,0,.46-.06.64-.18Z" />
                    <path class="cls-18"
                        d="M404.82,170.96s.05.04.04.08l-.07.31s-.04.05-.08.04c-.07-.03-.15-.04-.24-.04h-.08c-.22.01-.4.09-.54.25s-.21.34-.21.58v1.84s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-3.04s.02-.06.06-.06h.32s.06.02.06.06v.38s0,.02,0,.02c0,0,.01,0,.02,0,.09-.15.2-.27.33-.36.14-.09.29-.13.47-.13.14,0,.26.03.36.08Z" />
                    <path class="cls-18"
                        d="M405.46,174.01v-4.25s.02-.06.06-.06h.32s.06.02.06.06v2.56s0,.01,0,.02.01,0,.02,0l1.33-1.39s.05-.03.08-.03h.38s.04,0,.05.02c0,.02,0,.03-.01.05l-.8.89s-.01.02,0,.03l.96,2.09v.03s-.01.05-.05.05h-.34s-.06-.02-.07-.05l-.81-1.83s-.02-.02-.03,0l-.69.72s-.01.02-.01.03v1.06s-.02.06-.06.06h-.32s-.06-.02-.06-.06Z" />
                    <path class="cls-18"
                        d="M410.92,172.4v.2s-.02.06-.06.06h-2.02s-.03,0-.03.02c0,.23.02.38.04.44.05.19.14.33.29.44.15.11.34.16.58.16.17,0,.33-.04.46-.12.13-.08.24-.19.31-.34.02-.04.05-.05.09-.02l.24.14s.04.05.02.09c-.1.2-.26.36-.47.47-.21.11-.45.17-.72.17-.3,0-.54-.08-.74-.21s-.34-.33-.43-.58c-.08-.2-.11-.49-.11-.86,0-.17,0-.32,0-.43,0-.11.02-.21.05-.3.07-.27.21-.49.43-.65s.47-.24.78-.24c.38,0,.67.1.88.29s.33.46.38.81c.02.12.03.26.03.44ZM409.13,171.4c-.14.11-.22.25-.27.43-.02.09-.04.23-.05.42,0,.02,0,.03.03.03h1.62s.03,0,.03-.03c0-.19-.02-.32-.04-.4-.05-.19-.14-.34-.28-.45-.14-.11-.32-.17-.53-.17s-.37.05-.51.16Z" />
                    <path class="cls-18"
                        d="M413.12,171.28h-.71s-.03,0-.03.03v1.84c0,.2.04.34.13.42.09.08.23.12.42.12h.16s.06.02.06.06v.26s-.02.06-.06.06c-.05,0-.13,0-.23,0-.3,0-.53-.06-.68-.17-.15-.11-.23-.32-.23-.62v-1.98s0-.03-.02-.03h-.38s-.06-.02-.06-.06v-.24s.02-.06.06-.06h.38s.02,0,.02-.03v-.72s.02-.06.06-.06h.31s.06.02.06.06v.72s0,.03.03.03h.71s.06.02.06.06v.24s-.02.06-.06.06Z" />
                    <path class="cls-18"
                        d="M415.39,171.28h-.71s-.03,0-.03.03v1.84c0,.2.04.34.13.42.09.08.23.12.42.12h.16s.06.02.06.06v.26s-.02.06-.06.06c-.05,0-.13,0-.23,0-.3,0-.53-.06-.68-.17-.15-.11-.23-.32-.23-.62v-1.98s0-.03-.02-.03h-.38s-.06-.02-.06-.06v-.24s.02-.06.06-.06h.38s.02,0,.02-.03v-.72s.02-.06.06-.06h.31s.06.02.06.06v.72s0,.03.03.03h.71s.06.02.06.06v.24s-.02.06-.06.06Z" />
                    <path class="cls-18"
                        d="M418.67,172.4v.2s-.02.06-.06.06h-2.02s-.03,0-.03.02c0,.23.02.38.04.44.05.19.14.33.29.44.15.11.34.16.58.16.17,0,.33-.04.46-.12.13-.08.24-.19.31-.34.02-.04.05-.05.09-.02l.24.14s.04.05.02.09c-.1.2-.26.36-.47.47-.21.11-.45.17-.72.17-.3,0-.54-.08-.74-.21s-.34-.33-.43-.58c-.08-.2-.11-.49-.11-.86,0-.17,0-.32,0-.43,0-.11.02-.21.05-.3.07-.27.21-.49.43-.65s.47-.24.78-.24c.38,0,.67.1.88.29s.33.46.38.81c.02.12.03.26.03.44ZM416.88,171.4c-.14.11-.22.25-.27.43-.02.09-.04.23-.05.42,0,.02,0,.03.03.03h1.62s.03,0,.03-.03c0-.19-.02-.32-.04-.4-.05-.19-.14-.34-.28-.45-.14-.11-.32-.17-.53-.17s-.37.05-.51.16Z" />
                    <path class="cls-18"
                        d="M421.48,171.15c.2.19.31.45.31.76v2.1s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-.26s0-.01,0-.02c0,0-.01,0-.02,0-.1.12-.24.22-.41.28-.17.06-.35.1-.56.1-.3,0-.54-.08-.74-.22-.2-.15-.3-.38-.3-.69s.11-.56.34-.74c.23-.18.54-.27.95-.27h.72s.03,0,.03-.03v-.24c0-.21-.06-.38-.18-.5-.12-.12-.3-.18-.55-.18-.2,0-.36.04-.49.12s-.2.18-.23.32c-.01.04-.04.06-.08.06l-.34-.04s-.07-.03-.06-.05c.03-.23.16-.42.38-.57.22-.15.49-.22.81-.22.38,0,.66.1.87.29ZM421.07,173.56c.18-.12.27-.29.27-.5v-.49s0-.03-.03-.03h-.66c-.27,0-.5.06-.66.17-.17.11-.25.27-.25.47,0,.18.06.32.19.42s.29.14.5.14c.25,0,.46-.06.64-.18Z" />
                    <path class="cls-18"
                        d="M426.65,171.15c.18.19.27.44.27.77v2.09s-.02.06-.06.06h-.31s-.06-.02-.06-.06v-2.01c0-.23-.07-.41-.2-.55-.13-.14-.3-.2-.52-.2s-.4.07-.53.2c-.14.13-.2.31-.2.54v2.02s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-2.01c0-.23-.06-.41-.19-.55-.13-.14-.3-.2-.52-.2s-.4.07-.53.2c-.13.13-.2.31-.2.54v2.02s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-3.04s.02-.06.06-.06h.32s.06.02.06.06v.25s0,.01,0,.02.01,0,.02,0c.09-.12.21-.21.35-.27s.3-.09.48-.09c.22,0,.4.04.56.13.15.09.27.21.35.38,0,.02.02.02.03,0,.09-.17.22-.3.39-.38.17-.08.36-.13.57-.13.31,0,.56.09.74.28Z" />
                    <path class="cls-18"
                        d="M322.71,185.95h.33s.06.02.06.06v4.25s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-3.47s0-.02-.01-.03c0,0-.01,0-.02.01l-1.02,1.56s-.05.04-.08.04h-.16s-.05-.01-.08-.04l-1.02-1.55s-.01-.02-.02-.01c0,0-.01.01-.01.03v3.46s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-4.25s.02-.06.06-.06h.32s.06.01.08.04l1.17,1.78s.01,0,.02,0c0,0,.01,0,.02,0l1.16-1.77s.05-.04.08-.04Z" />
                    <path class="cls-18"
                        d="M326.15,187.16h.32s.06.02.06.06v3.04s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-.27s0-.01-.01-.02c0,0-.01,0-.02,0-.17.26-.45.39-.83.39-.2,0-.38-.04-.55-.12s-.3-.2-.4-.35c-.1-.15-.15-.34-.15-.56v-2.11s.02-.06.06-.06h.32s.06.02.06.06v1.98c0,.24.07.43.2.57.13.14.31.21.54.21s.43-.07.57-.21c.14-.14.21-.33.21-.56v-1.98s.02-.06.06-.06Z" />
                    <path class="cls-18"
                        d="M327.34,191.51v-.25s.02-.06.06-.06h.03c.16,0,.29-.03.38-.07.09-.04.17-.12.23-.24.06-.12.13-.3.21-.54,0-.02,0-.03,0-.04l-1.06-3.08v-.03s.01-.04.05-.04h.33s.06.02.08.05l.81,2.52s0,.01.02.01c0,0,.01,0,.02-.01l.8-2.52s.04-.05.08-.05h.33s.06.02.05.07l-1.16,3.37c-.1.28-.19.49-.28.62-.09.13-.2.23-.33.28s-.32.08-.55.08h-.04s-.04-.02-.04-.06Z" />
                    <path class="cls-18"
                        d="M334.21,188.74c0,.33-.03.59-.08.77-.08.27-.21.48-.41.63-.2.15-.45.23-.75.23-.16,0-.31-.03-.45-.1s-.25-.16-.34-.28c0,0-.02-.01-.02,0,0,0,0,0,0,.02v1.47s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-4.25s.02-.06.06-.06h.32s.06.02.06.06v.26s0,.01,0,.02c0,0,.01,0,.02,0,.09-.12.21-.21.34-.28s.29-.1.45-.1c.29,0,.54.08.73.23.2.15.33.35.41.61.06.2.09.46.09.79ZM333.72,189.22c.02-.11.03-.27.03-.48s0-.35-.02-.46c-.02-.11-.05-.21-.09-.3-.05-.15-.14-.27-.27-.36-.13-.09-.28-.13-.45-.13s-.31.04-.42.13c-.11.09-.2.2-.25.35-.04.08-.06.18-.08.29s-.02.27-.02.47,0,.36.02.47.05.22.09.3c.05.14.13.26.25.34.11.09.25.13.41.13.17,0,.31-.04.43-.12.12-.08.21-.19.27-.33.04-.09.08-.19.1-.31Z" />
                    <path class="cls-18"
                        d="M336.74,187.21s.05.04.04.08l-.07.31s-.04.05-.08.04c-.07-.03-.15-.04-.24-.04h-.08c-.22.01-.4.09-.54.25s-.21.34-.21.58v1.84s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-3.04s.02-.06.06-.06h.32s.06.02.06.06v.38s0,.02,0,.02c0,0,.01,0,.02,0,.09-.15.2-.27.33-.36.14-.09.29-.13.47-.13.14,0,.26.03.36.08Z" />
                    <path class="cls-18"
                        d="M337.79,190.14c-.22-.15-.37-.37-.45-.64-.06-.19-.09-.45-.09-.77s.03-.57.09-.76c.08-.27.23-.48.44-.63s.48-.23.79-.23.55.08.77.23c.21.15.36.36.44.62.06.18.09.44.09.77s-.03.59-.09.77c-.08.27-.23.48-.44.64-.21.15-.47.23-.77.23s-.56-.08-.77-.23ZM339.07,189.82c.14-.11.24-.26.29-.45.04-.15.06-.36.06-.63s-.02-.49-.05-.63c-.05-.19-.15-.34-.29-.45s-.32-.17-.52-.17-.38.06-.52.17c-.14.11-.24.26-.29.45-.03.15-.05.36-.05.63s.02.49.05.63c.05.19.14.34.28.45.14.11.32.17.53.17s.37-.05.51-.17Z" />
                    <path class="cls-18"
                        d="M342.87,187.41c.2.2.3.46.3.79v2.06s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-1.97c0-.23-.07-.42-.21-.56-.14-.15-.32-.22-.54-.22s-.42.07-.56.21-.21.33-.21.56v1.99s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-3.04s.02-.06.06-.06h.32s.06.02.06.06v.27s0,.01,0,.02.01,0,.02,0c.18-.26.46-.39.84-.39.33,0,.6.1.8.29Z" />
                    <path class="cls-18"
                        d="M345.49,187.53h-.71s-.03,0-.03.03v1.84c0,.2.04.34.13.42.09.08.23.12.42.12h.16s.06.02.06.06v.26s-.02.06-.06.06c-.05,0-.13,0-.23,0-.3,0-.53-.06-.68-.17-.15-.11-.23-.32-.23-.62v-1.98s0-.03-.02-.03h-.38s-.06-.02-.06-.06v-.24s.02-.06.06-.06h.38s.02,0,.02-.03v-.72s.02-.06.06-.06h.31s.06.02.06.06v.72s0,.03.03.03h.71s.06.02.06.06v.24s-.02.06-.06.06Z" />
                    <path class="cls-18"
                        d="M346.74,190.14c-.22-.15-.37-.37-.45-.64-.06-.19-.09-.45-.09-.77s.03-.57.09-.76c.08-.27.23-.48.44-.63s.48-.23.79-.23.55.08.77.23c.21.15.36.36.44.62.06.18.09.44.09.77s-.03.59-.09.77c-.08.27-.23.48-.44.64-.21.15-.47.23-.77.23s-.56-.08-.77-.23ZM348.03,189.82c.14-.11.24-.26.29-.45.04-.15.06-.36.06-.63s-.02-.49-.05-.63c-.05-.19-.15-.34-.29-.45s-.32-.17-.52-.17-.38.06-.52.17c-.14.11-.24.26-.29.45-.03.15-.05.36-.05.63s.02.49.05.63c.05.19.14.34.28.45.14.11.32.17.53.17s.37-.05.51-.17Z" />
                    <path class="cls-18"
                        d="M353.52,188.74c0,.33-.03.59-.08.77-.08.27-.21.48-.41.63-.2.15-.45.23-.75.23-.16,0-.31-.03-.45-.1s-.25-.16-.34-.28c0,0-.02-.01-.02,0,0,0,0,0,0,.02v1.47s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-4.25s.02-.06.06-.06h.32s.06.02.06.06v.26s0,.01,0,.02c0,0,.01,0,.02,0,.09-.12.21-.21.34-.28s.29-.1.45-.1c.29,0,.54.08.73.23.2.15.33.35.41.61.06.2.09.46.09.79ZM353.03,189.22c.02-.11.03-.27.03-.48s0-.35-.02-.46c-.02-.11-.05-.21-.09-.3-.05-.15-.14-.27-.27-.36-.13-.09-.28-.13-.45-.13s-.31.04-.42.13c-.11.09-.2.2-.25.35-.04.08-.06.18-.08.29s-.02.27-.02.47,0,.36.02.47.05.22.09.3c.05.14.13.26.25.34.11.09.25.13.41.13.17,0,.31-.04.43-.12.12-.08.21-.19.27-.33.04-.09.08-.19.1-.31Z" />
                    <path class="cls-18"
                        d="M354.8,190.14c-.22-.15-.37-.37-.45-.64-.06-.19-.09-.45-.09-.77s.03-.57.09-.76c.08-.27.23-.48.44-.63s.48-.23.79-.23.55.08.77.23c.21.15.36.36.44.62.06.18.09.44.09.77s-.03.59-.09.77c-.08.27-.23.48-.44.64-.21.15-.47.23-.77.23s-.56-.08-.77-.23ZM356.09,189.82c.14-.11.24-.26.29-.45.04-.15.06-.36.06-.63s-.02-.49-.05-.63c-.05-.19-.15-.34-.29-.45s-.32-.17-.52-.17-.38.06-.52.17c-.14.11-.24.26-.29.45-.03.15-.05.36-.05.63s.02.49.05.63c.05.19.14.34.28.45.14.11.32.17.53.17s.37-.05.51-.17Z" />
                    <path class="cls-18"
                        d="M359.8,185.95h.32s.06.02.06.06v4.25s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-.26s0-.01,0-.02-.01,0-.02,0c-.09.12-.21.21-.34.28-.14.07-.29.1-.45.1-.29,0-.54-.08-.73-.22-.2-.15-.33-.35-.41-.61-.06-.19-.09-.45-.09-.79s.03-.6.08-.78c.08-.27.21-.48.41-.63s.45-.23.74-.23c.16,0,.31.03.45.1.14.06.25.16.34.28,0,0,.02.01.02,0s0,0,0-.02v-1.47s.02-.06.06-.06ZM359.71,189.21c.02-.11.02-.27.02-.47s0-.36-.02-.47c-.02-.12-.05-.22-.09-.3-.05-.14-.13-.25-.25-.34-.11-.08-.25-.13-.41-.13-.17,0-.31.04-.43.12-.12.08-.21.19-.26.33-.05.09-.09.2-.11.31-.02.12-.04.27-.04.48s0,.35.03.46.05.21.09.3c.05.15.14.27.27.36.13.09.28.13.45.13.17,0,.31-.04.42-.13.11-.09.2-.2.25-.35.04-.08.06-.18.08-.29Z" />
                    <path class="cls-18"
                        d="M362.82,187.21s.05.04.04.08l-.07.31s-.04.05-.08.04c-.07-.03-.15-.04-.24-.04h-.08c-.22.01-.4.09-.54.25s-.21.34-.21.58v1.84s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-3.04s.02-.06.06-.06h.32s.06.02.06.06v.38s0,.02,0,.02c0,0,.01,0,.02,0,.09-.15.2-.27.33-.36.14-.09.29-.13.47-.13.14,0,.26.03.36.08Z" />
                    <path class="cls-18"
                        d="M365.42,187.4c.2.19.31.45.31.76v2.1s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-.26s0-.01,0-.02c0,0-.01,0-.02,0-.1.12-.24.22-.41.28-.17.06-.35.1-.56.1-.3,0-.54-.08-.74-.22-.2-.15-.3-.38-.3-.69s.11-.56.34-.74c.23-.18.54-.27.95-.27h.72s.03,0,.03-.03v-.24c0-.21-.06-.38-.18-.5-.12-.12-.3-.18-.55-.18-.2,0-.36.04-.49.12s-.2.18-.23.32c-.01.04-.04.06-.08.06l-.34-.04s-.07-.03-.06-.05c.03-.23.16-.42.38-.57.22-.15.49-.22.81-.22.38,0,.66.1.87.29ZM365.01,189.81c.18-.12.27-.29.27-.5v-.49s0-.03-.03-.03h-.66c-.27,0-.5.06-.66.17-.17.11-.25.27-.25.47,0,.18.06.32.19.42s.29.14.5.14c.25,0,.46-.06.64-.18ZM364.29,186.72s0-.03,0-.05l.38-.68s.04-.04.08-.04h.28s.04,0,.05.02c0,.01,0,.03,0,.05l-.42.68s-.05.04-.08.04h-.22s-.04,0-.05-.02Z" />
                    <path class="cls-18"
                        d="M367.06,190.24c-.18-.08-.32-.17-.42-.3-.1-.12-.15-.26-.15-.42v-.08s.02-.06.06-.06h.3s.06.02.06.06v.05c0,.14.07.26.21.36.14.1.32.15.54.15s.39-.05.53-.15c.13-.1.2-.22.2-.37,0-.1-.03-.19-.1-.26-.07-.07-.15-.12-.24-.16-.09-.04-.24-.08-.43-.14-.23-.07-.42-.13-.57-.2-.15-.07-.28-.16-.38-.28-.1-.12-.15-.27-.15-.45,0-.27.1-.48.31-.64.21-.16.48-.24.82-.24.23,0,.43.04.61.11.18.08.31.18.41.31.1.13.14.28.14.44v.02s-.02.06-.06.06h-.29s-.06-.02-.06-.06v-.02c0-.14-.07-.26-.2-.36s-.32-.14-.55-.14c-.21,0-.38.04-.51.13-.13.09-.19.2-.19.35,0,.14.06.25.19.32.12.08.32.15.58.23.24.07.44.14.59.2.15.06.28.15.39.27s.16.27.16.47c0,.27-.11.49-.32.65-.21.16-.49.24-.84.24-.23,0-.44-.04-.62-.11Z" />
                    <path class="cls-18"
                        d="M372.92,185.95h.32s.06.02.06.06v4.25s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-.26s0-.01,0-.02-.01,0-.02,0c-.09.12-.21.21-.34.28-.14.07-.29.1-.45.1-.29,0-.54-.08-.73-.22-.2-.15-.33-.35-.41-.61-.06-.19-.09-.45-.09-.79s.03-.6.08-.78c.08-.27.21-.48.41-.63s.45-.23.74-.23c.16,0,.31.03.45.1.14.06.25.16.34.28,0,0,.02.01.02,0s0,0,0-.02v-1.47s.02-.06.06-.06ZM372.83,189.21c.02-.11.02-.27.02-.47s0-.36-.02-.47c-.02-.12-.05-.22-.09-.3-.05-.14-.13-.25-.25-.34-.11-.08-.25-.13-.41-.13-.17,0-.31.04-.43.12-.12.08-.21.19-.26.33-.05.09-.09.2-.11.31-.02.12-.04.27-.04.48s0,.35.03.46.05.21.09.3c.05.15.14.27.27.36.13.09.28.13.45.13.17,0,.31-.04.42-.13.11-.09.2-.2.25-.35.04-.08.06-.18.08-.29Z" />
                    <path class="cls-18"
                        d="M376.76,188.65v.2s-.02.06-.06.06h-2.02s-.03,0-.03.02c0,.23.02.38.04.44.05.19.14.33.29.44.15.11.34.16.58.16.17,0,.33-.04.46-.12.13-.08.24-.19.31-.34.02-.04.05-.05.09-.02l.24.14s.04.05.02.09c-.1.2-.26.36-.47.47-.21.11-.45.17-.72.17-.3,0-.54-.08-.74-.21s-.34-.33-.43-.58c-.08-.2-.11-.49-.11-.86,0-.17,0-.32,0-.43,0-.11.02-.21.05-.3.07-.27.21-.49.43-.65s.47-.24.78-.24c.38,0,.67.1.88.29s.33.46.38.81c.02.12.03.26.03.44ZM374.97,187.65c-.14.11-.22.25-.27.43-.02.09-.04.23-.05.42,0,.02,0,.03.03.03h1.62s.03,0,.03-.03c0-.19-.02-.32-.04-.4-.05-.19-.14-.34-.28-.45-.14-.11-.32-.17-.53-.17s-.37.05-.51.16Z" />
                    <path class="cls-18"
                        d="M378,190.24c-.18-.08-.32-.17-.42-.3-.1-.12-.15-.26-.15-.42v-.08s.02-.06.06-.06h.3s.06.02.06.06v.05c0,.14.07.26.21.36.14.1.32.15.54.15s.39-.05.53-.15c.13-.1.2-.22.2-.37,0-.1-.03-.19-.1-.26-.07-.07-.15-.12-.24-.16-.09-.04-.24-.08-.43-.14-.23-.07-.42-.13-.57-.2-.15-.07-.28-.16-.38-.28-.1-.12-.15-.27-.15-.45,0-.27.1-.48.31-.64.21-.16.48-.24.82-.24.23,0,.43.04.61.11.18.08.31.18.41.31.1.13.14.28.14.44v.02s-.02.06-.06.06h-.29s-.06-.02-.06-.06v-.02c0-.14-.07-.26-.2-.36s-.32-.14-.55-.14c-.21,0-.38.04-.51.13-.13.09-.19.2-.19.35,0,.14.06.25.19.32.12.08.32.15.58.23.24.07.44.14.59.2.15.06.28.15.39.27s.16.27.16.47c0,.27-.11.49-.32.65-.21.16-.49.24-.84.24-.23,0-.44-.04-.62-.11Z" />
                    <path class="cls-18"
                        d="M381.01,190.14c-.21-.16-.36-.37-.44-.63-.05-.18-.08-.44-.08-.78,0-.3.03-.55.08-.76.08-.26.22-.47.44-.62s.47-.23.77-.23.57.08.79.23.37.35.43.58c.02.08.03.14.04.19h0s-.02.06-.06.07l-.31.04h-.01s-.05-.02-.06-.06l-.02-.09c-.04-.16-.13-.29-.28-.4-.15-.11-.32-.17-.53-.17s-.38.06-.52.17-.23.26-.27.44c-.04.16-.06.37-.06.63,0,.28.02.49.06.64.04.19.13.34.27.45s.31.17.52.17.38-.05.53-.16c.15-.11.24-.24.28-.41v-.05s.04-.06.08-.05l.31.05s.06.03.06.07l-.02.12c-.06.24-.21.44-.43.59-.22.15-.49.22-.79.22s-.56-.08-.77-.23Z" />
                    <path class="cls-18"
                        d="M385.82,187.16h.32s.06.02.06.06v3.04s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-.27s0-.01-.01-.02c0,0-.01,0-.02,0-.17.26-.45.39-.83.39-.2,0-.38-.04-.55-.12s-.3-.2-.4-.35c-.1-.15-.15-.34-.15-.56v-2.11s.02-.06.06-.06h.32s.06.02.06.06v1.98c0,.24.07.43.2.57.13.14.31.21.54.21s.43-.07.57-.21c.14-.14.21-.33.21-.56v-1.98s.02-.06.06-.06Z" />
                    <path class="cls-18"
                        d="M389.7,188.74c0,.34-.03.61-.09.79-.08.26-.22.46-.41.61-.2.15-.44.22-.73.22-.17,0-.32-.03-.46-.1-.14-.07-.25-.16-.34-.29,0,0,0-.01-.02,0,0,0-.01,0-.01.02v.27s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-4.25s.02-.06.06-.06h.32s.06.02.06.06v1.47s0,.01,0,.02c0,0,.01,0,.02,0,.09-.12.21-.21.34-.28.14-.07.29-.1.46-.1.29,0,.54.08.74.23s.34.36.41.63c.05.19.07.45.07.78ZM389.23,189.2c.02-.11.03-.26.03-.46s-.01-.36-.03-.48c-.02-.11-.06-.22-.11-.31-.05-.14-.14-.25-.26-.33-.12-.08-.26-.12-.43-.12-.16,0-.29.04-.41.13-.11.09-.2.2-.25.34-.04.09-.07.19-.09.3-.02.11-.03.27-.03.48s0,.36.03.47c.02.11.05.21.08.3.05.15.14.26.25.35s.25.13.42.13.33-.04.45-.13.22-.21.27-.36c.04-.09.07-.19.09-.3Z" />
                    <path class="cls-18"
                        d="M392.23,187.21s.05.04.04.08l-.07.31s-.04.05-.08.04c-.07-.03-.15-.04-.24-.04h-.08c-.22.01-.4.09-.54.25s-.21.34-.21.58v1.84s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-3.04s.02-.06.06-.06h.32s.06.02.06.06v.38s0,.02,0,.02c0,0,.01,0,.02,0,.09-.15.2-.27.33-.36.14-.09.29-.13.47-.13.14,0,.26.03.36.08Z" />
                    <path class="cls-18"
                        d="M392.93,186.48c-.07-.07-.1-.15-.1-.25s.03-.19.1-.25.15-.1.25-.1.18.03.25.1.1.15.1.25-.03.18-.1.25c-.07.07-.15.1-.25.1s-.18-.03-.25-.1ZM392.94,190.25v-3.04s.02-.06.06-.06h.32s.06.02.06.06v3.04s-.02.06-.06.06h-.32s-.06-.02-.06-.06Z" />
                    <path class="cls-18"
                        d="M396.1,187.21s.05.04.04.08l-.07.31s-.04.05-.08.04c-.07-.03-.15-.04-.24-.04h-.08c-.22.01-.4.09-.54.25s-.21.34-.21.58v1.84s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-3.04s.02-.06.06-.06h.32s.06.02.06.06v.38s0,.02,0,.02c0,0,.01,0,.02,0,.09-.15.2-.27.33-.36.14-.09.29-.13.47-.13.14,0,.26.03.36.08Z" />
                    <path class="cls-18"
                        d="M399.35,187.53h-.71s-.03,0-.03.03v1.84c0,.2.04.34.13.42.09.08.23.12.42.12h.16s.06.02.06.06v.26s-.02.06-.06.06c-.05,0-.13,0-.23,0-.3,0-.53-.06-.68-.17-.15-.11-.23-.32-.23-.62v-1.98s0-.03-.02-.03h-.38s-.06-.02-.06-.06v-.24s.02-.06.06-.06h.38s.02,0,.02-.03v-.72s.02-.06.06-.06h.31s.06.02.06.06v.72s0,.03.03.03h.71s.06.02.06.06v.24s-.02.06-.06.06Z" />
                    <path class="cls-18"
                        d="M400.61,190.14c-.22-.15-.37-.37-.45-.64-.06-.19-.09-.45-.09-.77s.03-.57.09-.76c.08-.27.23-.48.44-.63s.48-.23.79-.23.55.08.77.23c.21.15.36.36.44.62.06.18.09.44.09.77s-.03.59-.09.77c-.08.27-.23.48-.44.64-.21.15-.47.23-.77.23s-.56-.08-.77-.23ZM401.9,189.82c.14-.11.24-.26.29-.45.04-.15.06-.36.06-.63s-.02-.49-.05-.63c-.05-.19-.15-.34-.29-.45s-.32-.17-.52-.17-.38.06-.52.17c-.14.11-.24.26-.29.45-.03.15-.05.36-.05.63s.02.49.05.63c.05.19.14.34.28.45.14.11.32.17.53.17s.37-.05.51-.17Z" />
                    <path class="cls-18"
                        d="M405.61,185.95h.32s.06.02.06.06v4.25s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-.26s0-.01,0-.02-.01,0-.02,0c-.09.12-.21.21-.34.28-.14.07-.29.1-.45.1-.29,0-.54-.08-.73-.22-.2-.15-.33-.35-.41-.61-.06-.19-.09-.45-.09-.79s.03-.6.08-.78c.08-.27.21-.48.41-.63s.45-.23.74-.23c.16,0,.31.03.45.1.14.06.25.16.34.28,0,0,.02.01.02,0s0,0,0-.02v-1.47s.02-.06.06-.06ZM405.52,189.21c.02-.11.02-.27.02-.47s0-.36-.02-.47c-.02-.12-.05-.22-.09-.3-.05-.14-.13-.25-.25-.34-.11-.08-.25-.13-.41-.13-.17,0-.31.04-.43.12-.12.08-.21.19-.26.33-.05.09-.09.2-.11.31-.02.12-.04.27-.04.48s0,.35.03.46.05.21.09.3c.05.15.14.27.27.36.13.09.28.13.45.13.17,0,.31-.04.42-.13.11-.09.2-.2.25-.35.04-.08.06-.18.08-.29Z" />
                    <path class="cls-18"
                        d="M407.43,190.14c-.22-.15-.37-.37-.45-.64-.06-.19-.09-.45-.09-.77s.03-.57.09-.76c.08-.27.23-.48.44-.63s.48-.23.79-.23.55.08.77.23c.21.15.36.36.44.62.06.18.09.44.09.77s-.03.59-.09.77c-.08.27-.23.48-.44.64-.21.15-.47.23-.77.23s-.56-.08-.77-.23ZM408.72,189.82c.14-.11.24-.26.29-.45.04-.15.06-.36.06-.63s-.02-.49-.05-.63c-.05-.19-.15-.34-.29-.45s-.32-.17-.52-.17-.38.06-.52.17c-.14.11-.24.26-.29.45-.03.15-.05.36-.05.63s.02.49.05.63c.05.19.14.34.28.45.14.11.32.17.53.17s.37-.05.51-.17Z" />
                    <path class="cls-18"
                        d="M411.65,190.26v-4.25s.02-.06.06-.06h.32s.06.02.06.06v4.25s-.02.06-.06.06h-.32s-.06-.02-.06-.06Z" />
                    <path class="cls-18"
                        d="M413.52,190.14c-.22-.15-.37-.37-.45-.64-.06-.19-.09-.45-.09-.77s.03-.57.09-.76c.08-.27.23-.48.44-.63s.48-.23.79-.23.55.08.77.23c.21.15.36.36.44.62.06.18.09.44.09.77s-.03.59-.09.77c-.08.27-.23.48-.44.64-.21.15-.47.23-.77.23s-.56-.08-.77-.23ZM414.81,189.82c.14-.11.24-.26.29-.45.04-.15.06-.36.06-.63s-.02-.49-.05-.63c-.05-.19-.15-.34-.29-.45s-.32-.17-.52-.17-.38.06-.52.17c-.14.11-.24.26-.29.45-.03.15-.05.36-.05.63s.02.49.05.63c.05.19.14.34.28.45.14.11.32.17.53.17s.37-.05.51-.17Z" />
                    <path class="cls-18"
                        d="M419.77,187.16h.32s.06.02.06.06v4.25s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-1.47s0-.01,0-.02-.01,0-.02,0c-.09.12-.21.21-.34.28-.14.06-.29.1-.45.1-.3,0-.54-.08-.74-.23-.2-.15-.34-.36-.41-.63-.05-.17-.08-.43-.08-.77s.03-.61.09-.79c.07-.26.21-.46.41-.61.2-.15.44-.23.73-.23.17,0,.32.03.45.1.13.07.25.16.34.28,0,0,.02.01.02,0,0,0,0,0,0-.02v-.26s.02-.06.06-.06ZM419.68,189.21c.02-.11.02-.27.02-.47s0-.36-.02-.47c-.02-.11-.04-.21-.08-.29-.05-.15-.14-.26-.25-.35-.11-.09-.25-.13-.42-.13-.18,0-.33.04-.45.13-.13.09-.22.21-.27.36-.04.09-.07.19-.09.3-.02.11-.03.26-.03.46s.01.36.04.48c.02.12.06.22.11.31.05.14.14.25.26.33.12.08.26.12.43.12.16,0,.29-.04.41-.13.11-.08.2-.2.25-.34.04-.09.07-.19.09-.3Z" />
                    <path class="cls-18"
                        d="M423.17,187.16h.32s.06.02.06.06v3.04s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-.27s0-.01-.01-.02c0,0-.01,0-.02,0-.17.26-.45.39-.83.39-.2,0-.38-.04-.55-.12s-.3-.2-.4-.35c-.1-.15-.15-.34-.15-.56v-2.11s.02-.06.06-.06h.32s.06.02.06.06v1.98c0,.24.07.43.2.57.13.14.31.21.54.21s.43-.07.57-.21c.14-.14.21-.33.21-.56v-1.98s.02-.06.06-.06Z" />
                    <path class="cls-18"
                        d="M427,188.65v.2s-.02.06-.06.06h-2.02s-.03,0-.03.02c0,.23.02.38.04.44.05.19.14.33.29.44.15.11.34.16.58.16.17,0,.33-.04.46-.12.13-.08.24-.19.31-.34.02-.04.05-.05.09-.02l.24.14s.04.05.02.09c-.1.2-.26.36-.47.47-.21.11-.45.17-.72.17-.3,0-.54-.08-.74-.21s-.34-.33-.43-.58c-.08-.2-.11-.49-.11-.86,0-.17,0-.32,0-.43,0-.11.02-.21.05-.3.07-.27.21-.49.43-.65s.47-.24.78-.24c.38,0,.67.1.88.29s.33.46.38.81c.02.12.03.26.03.44ZM425.21,187.65c-.14.11-.22.25-.27.43-.02.09-.04.23-.05.42,0,.02,0,.03.03.03h1.62s.03,0,.03-.03c0-.19-.02-.32-.04-.4-.05-.19-.14-.34-.28-.45-.14-.11-.32-.17-.53-.17s-.37.05-.51.16Z" />
                    <path class="cls-18"
                        d="M356.13,195.65h-.71s-.03,0-.03.03v1.84c0,.2.04.34.13.42.09.08.23.12.42.12h.16s.06.02.06.06v.26s-.02.06-.06.06c-.05,0-.13,0-.23,0-.3,0-.53-.06-.68-.17-.15-.11-.23-.32-.23-.62v-1.98s0-.03-.02-.03h-.38s-.06-.02-.06-.06v-.24s.02-.06.06-.06h.38s.02,0,.02-.03v-.72s.02-.06.06-.06h.31s.06.02.06.06v.72s0,.03.03.03h.71s.06.02.06.06v.24s-.02.06-.06.06Z" />
                    <path class="cls-18"
                        d="M359.4,196.77v.2s-.02.06-.06.06h-2.02s-.03,0-.03.02c0,.23.02.38.04.44.05.19.14.33.29.44.15.11.34.16.58.16.17,0,.33-.04.46-.12.13-.08.24-.19.31-.34.02-.04.05-.05.09-.02l.24.14s.04.05.02.09c-.1.2-.26.36-.47.47-.21.11-.45.17-.72.17-.3,0-.54-.08-.74-.21s-.34-.33-.43-.58c-.08-.2-.11-.49-.11-.86,0-.17,0-.32,0-.43,0-.11.02-.21.05-.3.07-.27.21-.49.43-.65s.47-.24.78-.24c.38,0,.67.1.88.29s.33.46.38.81c.02.12.03.26.03.44ZM357.61,195.78c-.14.11-.22.25-.27.43-.02.09-.04.23-.05.42,0,.02,0,.03.03.03h1.62s.03,0,.03-.03c0-.19-.02-.32-.04-.4-.05-.19-.14-.34-.28-.45-.14-.11-.32-.17-.53-.17s-.37.05-.51.16Z" />
                    <path class="cls-18"
                        d="M362.39,195.53c.2.2.3.46.3.79v2.06s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-1.97c0-.23-.07-.42-.21-.56-.14-.15-.32-.22-.54-.22s-.42.07-.56.21-.21.33-.21.56v1.99s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-3.04s.02-.06.06-.06h.32s.06.02.06.06v.27s0,.01,0,.02.01,0,.02,0c.18-.26.46-.39.84-.39.33,0,.6.1.8.29Z" />
                    <path class="cls-18"
                        d="M366.06,196.77v.2s-.02.06-.06.06h-2.02s-.03,0-.03.02c0,.23.02.38.04.44.05.19.14.33.29.44.15.11.34.16.58.16.17,0,.33-.04.46-.12.13-.08.24-.19.31-.34.02-.04.05-.05.09-.02l.24.14s.04.05.02.09c-.1.2-.26.36-.47.47-.21.11-.45.17-.72.17-.3,0-.54-.08-.74-.21s-.34-.33-.43-.58c-.08-.2-.11-.49-.11-.86,0-.17,0-.32,0-.43,0-.11.02-.21.05-.3.07-.27.21-.49.43-.65s.47-.24.78-.24c.38,0,.67.1.88.29s.33.46.38.81c.02.12.03.26.03.44ZM364.27,195.78c-.14.11-.22.25-.27.43-.02.09-.04.23-.05.42,0,.02,0,.03.03.03h1.62s.03,0,.03-.03c0-.19-.02-.32-.04-.4-.05-.19-.14-.34-.28-.45-.14-.11-.32-.17-.53-.17s-.37.05-.51.16Z" />
                    <path class="cls-18"
                        d="M370.88,195.52c.18.19.27.44.27.77v2.09s-.02.06-.06.06h-.31s-.06-.02-.06-.06v-2.01c0-.23-.07-.41-.2-.55-.13-.14-.3-.2-.52-.2s-.4.07-.53.2c-.14.13-.2.31-.2.54v2.02s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-2.01c0-.23-.06-.41-.19-.55-.13-.14-.3-.2-.52-.2s-.4.07-.53.2c-.13.13-.2.31-.2.54v2.02s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-3.04s.02-.06.06-.06h.32s.06.02.06.06v.25s0,.01,0,.02.01,0,.02,0c.09-.12.21-.21.35-.27s.3-.09.48-.09c.22,0,.4.04.56.13.15.09.27.21.35.38,0,.02.02.02.03,0,.09-.17.22-.3.39-.38.17-.08.36-.13.57-.13.31,0,.56.09.74.28Z" />
                    <path class="cls-18"
                        d="M372.5,198.26c-.22-.15-.37-.37-.45-.64-.06-.19-.09-.45-.09-.77s.03-.57.09-.76c.08-.27.23-.48.44-.63s.48-.23.79-.23.55.08.77.23c.21.15.36.36.44.62.06.18.09.44.09.77s-.03.59-.09.77c-.08.27-.23.48-.44.64-.21.15-.47.23-.77.23s-.56-.08-.77-.23ZM373.78,197.94c.14-.11.24-.26.29-.45.04-.15.06-.36.06-.63s-.02-.49-.05-.63c-.05-.19-.15-.34-.29-.45s-.32-.17-.52-.17-.38.06-.52.17c-.14.11-.24.26-.29.45-.03.15-.05.36-.05.63s.02.49.05.63c.05.19.14.34.28.45.14.11.32.17.53.17s.37-.05.51-.17Z" />
                    <path class="cls-18"
                        d="M375.89,198.36c-.18-.08-.32-.17-.42-.3-.1-.12-.15-.26-.15-.42v-.08s.02-.06.06-.06h.3s.06.02.06.06v.05c0,.14.07.26.21.36.14.1.32.15.54.15s.39-.05.53-.15c.13-.1.2-.22.2-.37,0-.1-.03-.19-.1-.26-.07-.07-.15-.12-.24-.16-.09-.04-.24-.08-.43-.14-.23-.07-.42-.13-.57-.2-.15-.07-.28-.16-.38-.28-.1-.12-.15-.27-.15-.45,0-.27.1-.48.31-.64.21-.16.48-.24.82-.24.23,0,.43.04.61.11.18.08.31.18.41.31.1.13.14.28.14.44v.02s-.02.06-.06.06h-.29s-.06-.02-.06-.06v-.02c0-.14-.07-.26-.2-.36s-.32-.14-.55-.14c-.21,0-.38.04-.51.13-.13.09-.19.2-.19.35,0,.14.06.25.19.32.12.08.32.15.58.23.24.07.44.14.59.2.15.06.28.15.39.27s.16.27.16.47c0,.27-.11.49-.32.65-.21.16-.49.24-.84.24-.23,0-.44-.04-.62-.11Z" />
                    <path class="cls-18"
                        d="M382.28,196.86c0,.33-.03.59-.08.77-.08.27-.21.48-.41.63-.2.15-.45.23-.75.23-.16,0-.31-.03-.45-.1s-.25-.16-.34-.28c0,0-.02-.01-.02,0,0,0,0,0,0,.02v1.47s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-4.25s.02-.06.06-.06h.32s.06.02.06.06v.26s0,.01,0,.02c0,0,.01,0,.02,0,.09-.12.21-.21.34-.28s.29-.1.45-.1c.29,0,.54.08.73.23.2.15.33.35.41.61.06.2.09.46.09.79ZM381.79,197.34c.02-.11.03-.27.03-.48s0-.35-.02-.46c-.02-.11-.05-.21-.09-.3-.05-.15-.14-.27-.27-.36-.13-.09-.28-.13-.45-.13s-.31.04-.42.13c-.11.09-.2.2-.25.35-.04.08-.06.18-.08.29s-.02.27-.02.47,0,.36.02.47.05.22.09.3c.05.14.13.26.25.34.11.09.25.13.41.13.17,0,.31-.04.43-.12.12-.08.21-.19.27-.33.04-.09.08-.19.1-.31Z" />
                    <path class="cls-18"
                        d="M384.81,195.33s.05.04.04.08l-.07.31s-.04.05-.08.04c-.07-.03-.15-.04-.24-.04h-.08c-.22.01-.4.09-.54.25s-.21.34-.21.58v1.84s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-3.04s.02-.06.06-.06h.32s.06.02.06.06v.38s0,.02,0,.02c0,0,.01,0,.02,0,.09-.15.2-.27.33-.36.14-.09.29-.13.47-.13.14,0,.26.03.36.08Z" />
                    <path class="cls-18"
                        d="M387.87,196.77v.2s-.02.06-.06.06h-2.02s-.03,0-.03.02c0,.23.02.38.04.44.05.19.14.33.29.44.15.11.34.16.58.16.17,0,.33-.04.46-.12.13-.08.24-.19.31-.34.02-.04.05-.05.09-.02l.24.14s.04.05.02.09c-.1.2-.26.36-.47.47-.21.11-.45.17-.72.17-.3,0-.54-.08-.74-.21s-.34-.33-.43-.58c-.08-.2-.11-.49-.11-.86,0-.17,0-.32,0-.43,0-.11.02-.21.05-.3.07-.27.21-.49.43-.65s.47-.24.78-.24c.38,0,.67.1.88.29s.33.46.38.81c.02.12.03.26.03.44ZM386.08,195.78c-.14.11-.22.25-.27.43-.02.09-.04.23-.05.42,0,.02,0,.03.03.03h1.62s.03,0,.03-.03c0-.19-.02-.32-.04-.4-.05-.19-.14-.34-.28-.45-.14-.11-.32-.17-.53-.17s-.37.05-.51.16Z" />
                    <path class="cls-18"
                        d="M391.3,196.86c0,.33-.03.59-.08.77-.08.27-.21.48-.41.63-.2.15-.45.23-.75.23-.16,0-.31-.03-.45-.1s-.25-.16-.34-.28c0,0-.02-.01-.02,0,0,0,0,0,0,.02v1.47s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-4.25s.02-.06.06-.06h.32s.06.02.06.06v.26s0,.01,0,.02c0,0,.01,0,.02,0,.09-.12.21-.21.34-.28s.29-.1.45-.1c.29,0,.54.08.73.23.2.15.33.35.41.61.06.2.09.46.09.79ZM390.82,197.34c.02-.11.03-.27.03-.48s0-.35-.02-.46c-.02-.11-.05-.21-.09-.3-.05-.15-.14-.27-.27-.36-.13-.09-.28-.13-.45-.13s-.31.04-.42.13c-.11.09-.2.2-.25.35-.04.08-.06.18-.08.29s-.02.27-.02.47,0,.36.02.47.05.22.09.3c.05.14.13.26.25.34.11.09.25.13.41.13.17,0,.31-.04.43-.12.12-.08.21-.19.27-.33.04-.09.08-.19.1-.31Z" />
                    <path class="cls-18"
                        d="M394.17,195.52c.2.19.31.45.31.76v2.1s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-.26s0-.01,0-.02c0,0-.01,0-.02,0-.1.12-.24.22-.41.28-.17.06-.35.1-.56.1-.3,0-.54-.08-.74-.22-.2-.15-.3-.38-.3-.69s.11-.56.34-.74c.23-.18.54-.27.95-.27h.72s.03,0,.03-.03v-.24c0-.21-.06-.38-.18-.5-.12-.12-.3-.18-.55-.18-.2,0-.36.04-.49.12s-.2.18-.23.32c-.01.04-.04.06-.08.06l-.34-.04s-.07-.03-.06-.05c.03-.23.16-.42.38-.57.22-.15.49-.22.81-.22.38,0,.66.1.87.29ZM393.76,197.93c.18-.12.27-.29.27-.5v-.49s0-.03-.03-.03h-.66c-.27,0-.5.06-.66.17-.17.11-.25.27-.25.47,0,.18.06.32.19.42s.29.14.5.14c.25,0,.46-.06.64-.18Z" />
                    <path class="cls-18"
                        d="M397.03,195.33s.05.04.04.08l-.07.31s-.04.05-.08.04c-.07-.03-.15-.04-.24-.04h-.08c-.22.01-.4.09-.54.25s-.21.34-.21.58v1.84s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-3.04s.02-.06.06-.06h.32s.06.02.06.06v.38s0,.02,0,.02c0,0,.01,0,.02,0,.09-.15.2-.27.33-.36.14-.09.29-.13.47-.13.14,0,.26.03.36.08Z" />
                    <path class="cls-18"
                        d="M399.62,195.52c.2.19.31.45.31.76v2.1s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-.26s0-.01,0-.02c0,0-.01,0-.02,0-.1.12-.24.22-.41.28-.17.06-.35.1-.56.1-.3,0-.54-.08-.74-.22-.2-.15-.3-.38-.3-.69s.11-.56.34-.74c.23-.18.54-.27.95-.27h.72s.03,0,.03-.03v-.24c0-.21-.06-.38-.18-.5-.12-.12-.3-.18-.55-.18-.2,0-.36.04-.49.12s-.2.18-.23.32c-.01.04-.04.06-.08.06l-.34-.04s-.07-.03-.06-.05c.03-.23.16-.42.38-.57.22-.15.49-.22.81-.22.38,0,.66.1.87.29ZM399.22,197.93c.18-.12.27-.29.27-.5v-.49s0-.03-.03-.03h-.66c-.27,0-.5.06-.66.17-.17.11-.25.27-.25.47,0,.18.06.32.19.42s.29.14.5.14c.25,0,.46-.06.64-.18Z" />
                    <path class="cls-18"
                        d="M402.88,194.07h.32s.06.02.06.06v4.25s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-.26s0-.01,0-.02-.01,0-.02,0c-.09.12-.21.21-.34.28-.14.07-.29.1-.45.1-.29,0-.54-.08-.73-.22-.2-.15-.33-.35-.41-.61-.06-.19-.09-.45-.09-.79s.03-.6.08-.78c.08-.27.21-.48.41-.63s.45-.23.74-.23c.16,0,.31.03.45.1.14.06.25.16.34.28,0,0,.02.01.02,0s0,0,0-.02v-1.47s.02-.06.06-.06ZM402.78,197.33c.02-.11.02-.27.02-.47s0-.36-.02-.47c-.02-.12-.05-.22-.09-.3-.05-.14-.13-.25-.25-.34-.11-.08-.25-.13-.41-.13-.17,0-.31.04-.43.12-.12.08-.21.19-.26.33-.05.09-.09.2-.11.31-.02.12-.04.27-.04.48s0,.35.03.46.05.21.09.3c.05.15.14.27.27.36.13.09.28.13.45.13.17,0,.31-.04.42-.13.11-.09.2-.2.25-.35.04-.08.06-.18.08-.29Z" />
                    <path class="cls-18"
                        d="M404.7,198.26c-.22-.15-.37-.37-.45-.64-.06-.19-.09-.45-.09-.77s.03-.57.09-.76c.08-.27.23-.48.44-.63s.48-.23.79-.23.55.08.77.23c.21.15.36.36.44.62.06.18.09.44.09.77s-.03.59-.09.77c-.08.27-.23.48-.44.64-.21.15-.47.23-.77.23s-.56-.08-.77-.23ZM405.98,197.94c.14-.11.24-.26.29-.45.04-.15.06-.36.06-.63s-.02-.49-.05-.63c-.05-.19-.15-.34-.29-.45s-.32-.17-.52-.17-.38.06-.52.17c-.14.11-.24.26-.29.45-.03.15-.05.36-.05.63s.02.49.05.63c.05.19.14.34.28.45.14.11.32.17.53.17s.37-.05.51-.17Z" />
                    <path class="cls-18"
                        d="M411.47,196.86c0,.33-.03.59-.08.77-.08.27-.21.48-.41.63-.2.15-.45.23-.75.23-.16,0-.31-.03-.45-.1s-.25-.16-.34-.28c0,0-.02-.01-.02,0,0,0,0,0,0,.02v1.47s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-4.25s.02-.06.06-.06h.32s.06.02.06.06v.26s0,.01,0,.02c0,0,.01,0,.02,0,.09-.12.21-.21.34-.28s.29-.1.45-.1c.29,0,.54.08.73.23.2.15.33.35.41.61.06.2.09.46.09.79ZM410.99,197.34c.02-.11.03-.27.03-.48s0-.35-.02-.46c-.02-.11-.05-.21-.09-.3-.05-.15-.14-.27-.27-.36-.13-.09-.28-.13-.45-.13s-.31.04-.42.13c-.11.09-.2.2-.25.35-.04.08-.06.18-.08.29s-.02.27-.02.47,0,.36.02.47.05.22.09.3c.05.14.13.26.25.34.11.09.25.13.41.13.17,0,.31-.04.43-.12.12-.08.21-.19.27-.33.04-.09.08-.19.1-.31Z" />
                    <path class="cls-18"
                        d="M414.33,195.52c.2.19.31.45.31.76v2.1s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-.26s0-.01,0-.02c0,0-.01,0-.02,0-.1.12-.24.22-.41.28-.17.06-.35.1-.56.1-.3,0-.54-.08-.74-.22-.2-.15-.3-.38-.3-.69s.11-.56.34-.74c.23-.18.54-.27.95-.27h.72s.03,0,.03-.03v-.24c0-.21-.06-.38-.18-.5-.12-.12-.3-.18-.55-.18-.2,0-.36.04-.49.12s-.2.18-.23.32c-.01.04-.04.06-.08.06l-.34-.04s-.07-.03-.06-.05c.03-.23.16-.42.38-.57.22-.15.49-.22.81-.22.38,0,.66.1.87.29ZM413.93,197.93c.18-.12.27-.29.27-.5v-.49s0-.03-.03-.03h-.66c-.27,0-.5.06-.66.17-.17.11-.25.27-.25.47,0,.18.06.32.19.42s.29.14.5.14c.25,0,.46-.06.64-.18Z" />
                    <path class="cls-18"
                        d="M417.2,195.33s.05.04.04.08l-.07.31s-.04.05-.08.04c-.07-.03-.15-.04-.24-.04h-.08c-.22.01-.4.09-.54.25s-.21.34-.21.58v1.84s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-3.04s.02-.06.06-.06h.32s.06.02.06.06v.38s0,.02,0,.02c0,0,.01,0,.02,0,.09-.15.2-.27.33-.36.14-.09.29-.13.47-.13.14,0,.26.03.36.08Z" />
                    <path class="cls-18"
                        d="M419.79,195.52c.2.19.31.45.31.76v2.1s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-.26s0-.01,0-.02c0,0-.01,0-.02,0-.1.12-.24.22-.41.28-.17.06-.35.1-.56.1-.3,0-.54-.08-.74-.22-.2-.15-.3-.38-.3-.69s.11-.56.34-.74c.23-.18.54-.27.95-.27h.72s.03,0,.03-.03v-.24c0-.21-.06-.38-.18-.5-.12-.12-.3-.18-.55-.18-.2,0-.36.04-.49.12s-.2.18-.23.32c-.01.04-.04.06-.08.06l-.34-.04s-.07-.03-.06-.05c.03-.23.16-.42.38-.57.22-.15.49-.22.81-.22.38,0,.66.1.87.29ZM419.38,197.93c.18-.12.27-.29.27-.5v-.49s0-.03-.03-.03h-.66c-.27,0-.5.06-.66.17-.17.11-.25.27-.25.47,0,.18.06.32.19.42s.29.14.5.14c.25,0,.46-.06.64-.18Z" />
                    <path class="cls-18"
                        d="M423.63,195.65h-.71s-.03,0-.03.03v1.84c0,.2.04.34.13.42.09.08.23.12.42.12h.16s.06.02.06.06v.26s-.02.06-.06.06c-.05,0-.13,0-.23,0-.3,0-.53-.06-.68-.17-.15-.11-.23-.32-.23-.62v-1.98s0-.03-.02-.03h-.38s-.06-.02-.06-.06v-.24s.02-.06.06-.06h.38s.02,0,.02-.03v-.72s.02-.06.06-.06h.31s.06.02.06.06v.72s0,.03.03.03h.71s.06.02.06.06v.24s-.02.06-.06.06Z" />
                    <path class="cls-18"
                        d="M424.6,194.61c-.07-.07-.1-.15-.1-.25s.03-.19.1-.25.15-.1.25-.1.18.03.25.1.1.15.1.25-.03.18-.1.25c-.07.07-.15.1-.25.1s-.18-.03-.25-.1ZM424.61,198.38v-3.04s.02-.06.06-.06h.32s.06.02.06.06v3.04s-.02.06-.06.06h-.32s-.06-.02-.06-.06Z" />
                    <path class="cls-18"
                        d="M426.25,198.33c-.06-.07-.1-.15-.1-.25,0-.1.03-.19.1-.25s.15-.1.25-.1.18.03.25.1.1.15.1.25-.03.18-.1.25-.15.1-.25.1-.19-.03-.25-.1Z" />
                    <path class="cls-18"
                        d="M335.68,214.65l-1.36-4.26v-.02s.01-.04.05-.04h.34s.06.02.08.05l1.14,3.66s0,.01.02.01c0,0,.01,0,.02-.01l1.14-3.66s.04-.05.08-.05h.33s.06.02.05.07l-1.38,4.26s-.04.05-.08.05h-.34s-.06-.02-.07-.05Z" />
                    <path class="cls-18"
                        d="M340.2,211.53h.32s.06.02.06.06v3.04s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-.27s0-.01-.01-.02c0,0-.01,0-.02,0-.17.26-.45.39-.83.39-.2,0-.38-.04-.55-.12s-.3-.2-.4-.35c-.1-.15-.15-.34-.15-.56v-2.11s.02-.06.06-.06h.32s.06.02.06.06v1.98c0,.24.07.43.2.57.13.14.31.21.54.21s.43-.07.57-.21c.14-.14.21-.33.21-.56v-1.98s.02-.06.06-.06Z" />
                    <path class="cls-18"
                        d="M344.03,213.02v.2s-.02.06-.06.06h-2.02s-.03,0-.03.02c0,.23.02.38.04.44.05.19.14.33.29.44.15.11.34.16.58.16.17,0,.33-.04.46-.12.13-.08.24-.19.31-.34.02-.04.05-.05.09-.02l.24.14s.04.05.02.09c-.1.2-.26.36-.47.47-.21.11-.45.17-.72.17-.3,0-.54-.08-.74-.21s-.34-.33-.43-.58c-.08-.2-.11-.49-.11-.86,0-.17,0-.32,0-.43,0-.11.02-.21.05-.3.07-.27.21-.49.43-.65s.47-.24.78-.24c.38,0,.67.1.88.29s.33.46.38.81c.02.12.03.26.03.44ZM342.24,212.03c-.14.11-.22.25-.27.43-.02.09-.04.23-.05.42,0,.02,0,.03.03.03h1.62s.03,0,.03-.03c0-.19-.02-.32-.04-.4-.05-.19-.14-.34-.28-.45-.14-.11-.32-.17-.53-.17s-.37.05-.51.16Z" />
                    <path class="cls-18"
                        d="M344.91,214.63v-4.25s.02-.06.06-.06h.32s.06.02.06.06v4.25s-.02.06-.06.06h-.32s-.06-.02-.06-.06Z" />
                    <path class="cls-18"
                        d="M347.08,214.65l-1.03-3.04v-.03s.01-.04.05-.04h.36s.06.02.08.05l.78,2.45s.01.01.02.01c0,0,.01,0,.02-.01l.78-2.45s.04-.05.08-.05h.36s.06.03.05.07l-1.02,3.04s-.04.05-.08.05h-.36s-.06-.02-.08-.05Z" />
                    <path class="cls-18"
                        d="M351.66,213.02v.2s-.02.06-.06.06h-2.02s-.03,0-.03.02c0,.23.02.38.04.44.05.19.14.33.29.44.15.11.34.16.58.16.17,0,.33-.04.46-.12.13-.08.24-.19.31-.34.02-.04.05-.05.09-.02l.24.14s.04.05.02.09c-.1.2-.26.36-.47.47-.21.11-.45.17-.72.17-.3,0-.54-.08-.74-.21s-.34-.33-.43-.58c-.08-.2-.11-.49-.11-.86,0-.17,0-.32,0-.43,0-.11.02-.21.05-.3.07-.27.21-.49.43-.65s.47-.24.78-.24c.38,0,.67.1.88.29s.33.46.38.81c.02.12.03.26.03.44ZM349.87,212.03c-.14.11-.22.25-.27.43-.02.09-.04.23-.05.42,0,.02,0,.03.03.03h1.62s.03,0,.03-.03c0-.19-.02-.32-.04-.4-.05-.19-.14-.34-.28-.45-.14-.11-.32-.17-.53-.17s-.37.05-.51.16Z" />
                    <path class="cls-18"
                        d="M356.34,213.11c0,.33-.03.59-.08.77-.08.27-.21.48-.41.63-.2.15-.45.23-.75.23-.16,0-.31-.03-.45-.1s-.25-.16-.34-.28c0,0-.02-.01-.02,0,0,0,0,0,0,.02v1.47s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-4.25s.02-.06.06-.06h.32s.06.02.06.06v.26s0,.01,0,.02c0,0,.01,0,.02,0,.09-.12.21-.21.34-.28s.29-.1.45-.1c.29,0,.54.08.73.23.2.15.33.35.41.61.06.2.09.46.09.79ZM355.86,213.59c.02-.11.03-.27.03-.48s0-.35-.02-.46c-.02-.11-.05-.21-.09-.3-.05-.15-.14-.27-.27-.36-.13-.09-.28-.13-.45-.13s-.31.04-.42.13c-.11.09-.2.2-.25.35-.04.08-.06.18-.08.29s-.02.27-.02.47,0,.36.02.47.05.22.09.3c.05.14.13.26.25.34.11.09.25.13.41.13.17,0,.31-.04.43-.12.12-.08.21-.19.27-.33.04-.09.08-.19.1-.31Z" />
                    <path class="cls-18"
                        d="M358.87,211.58s.05.04.04.08l-.07.31s-.04.05-.08.04c-.07-.03-.15-.04-.24-.04h-.08c-.22.01-.4.09-.54.25s-.21.34-.21.58v1.84s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-3.04s.02-.06.06-.06h.32s.06.02.06.06v.38s0,.02,0,.02c0,0,.01,0,.02,0,.09-.15.2-.27.33-.36.14-.09.29-.13.47-.13.14,0,.26.03.36.08Z" />
                    <path class="cls-18"
                        d="M359.92,214.51c-.22-.15-.37-.37-.45-.64-.06-.19-.09-.45-.09-.77s.03-.57.09-.76c.08-.27.23-.48.44-.63s.48-.23.79-.23.55.08.77.23c.21.15.36.36.44.62.06.18.09.44.09.77s-.03.59-.09.77c-.08.27-.23.48-.44.64-.21.15-.47.23-.77.23s-.56-.08-.77-.23ZM361.2,214.19c.14-.11.24-.26.29-.45.04-.15.06-.36.06-.63s-.02-.49-.05-.63c-.05-.19-.15-.34-.29-.45s-.32-.17-.52-.17-.38.06-.52.17c-.14.11-.24.26-.29.45-.03.15-.05.36-.05.63s.02.49.05.63c.05.19.14.34.28.45.14.11.32.17.53.17s.37-.05.51-.17Z" />
                    <path class="cls-18"
                        d="M365,211.78c.2.2.3.46.3.79v2.06s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-1.97c0-.23-.07-.42-.21-.56-.14-.15-.32-.22-.54-.22s-.42.07-.56.21-.21.33-.21.56v1.99s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-3.04s.02-.06.06-.06h.32s.06.02.06.06v.27s0,.01,0,.02.01,0,.02,0c.18-.26.46-.39.84-.39.33,0,.6.1.8.29Z" />
                    <path class="cls-18"
                        d="M367.62,211.9h-.71s-.03,0-.03.03v1.84c0,.2.04.34.13.42.09.08.23.12.42.12h.16s.06.02.06.06v.26s-.02.06-.06.06c-.05,0-.13,0-.23,0-.3,0-.53-.06-.68-.17-.15-.11-.23-.32-.23-.62v-1.98s0-.03-.02-.03h-.38s-.06-.02-.06-.06v-.24s.02-.06.06-.06h.38s.02,0,.02-.03v-.72s.02-.06.06-.06h.31s.06.02.06.06v.72s0,.03.03.03h.71s.06.02.06.06v.24s-.02.06-.06.06Z" />
                    <path class="cls-18"
                        d="M368.87,214.51c-.22-.15-.37-.37-.45-.64-.06-.19-.09-.45-.09-.77s.03-.57.09-.76c.08-.27.23-.48.44-.63s.48-.23.79-.23.55.08.77.23c.21.15.36.36.44.62.06.18.09.44.09.77s-.03.59-.09.77c-.08.27-.23.48-.44.64-.21.15-.47.23-.77.23s-.56-.08-.77-.23ZM370.16,214.19c.14-.11.24-.26.29-.45.04-.15.06-.36.06-.63s-.02-.49-.05-.63c-.05-.19-.15-.34-.29-.45s-.32-.17-.52-.17-.38.06-.52.17c-.14.11-.24.26-.29.45-.03.15-.05.36-.05.63s.02.49.05.63c.05.19.14.34.28.45.14.11.32.17.53.17s.37-.05.51-.17Z" />
                    <path class="cls-18"
                        d="M371.72,215.17s-.01-.03,0-.05l.27-1.34s.03-.06.07-.06h.27s.04,0,.05.02c.01.01.01.03,0,.05l-.35,1.34s-.03.06-.07.06h-.2s-.04,0-.05-.02Z" />
                    <path class="cls-18"
                        d="M377.14,213.02v.2s-.02.06-.06.06h-2.02s-.03,0-.03.02c0,.23.02.38.04.44.05.19.14.33.29.44.15.11.34.16.58.16.17,0,.33-.04.46-.12.13-.08.24-.19.31-.34.02-.04.05-.05.09-.02l.24.14s.04.05.02.09c-.1.2-.26.36-.47.47-.21.11-.45.17-.72.17-.3,0-.54-.08-.74-.21s-.34-.33-.43-.58c-.08-.2-.11-.49-.11-.86,0-.17,0-.32,0-.43,0-.11.02-.21.05-.3.07-.27.21-.49.43-.65s.47-.24.78-.24c.38,0,.67.1.88.29s.33.46.38.81c.02.12.03.26.03.44ZM375.35,212.03c-.14.11-.22.25-.27.43-.02.09-.04.23-.05.42,0,.02,0,.03.03.03h1.62s.03,0,.03-.03c0-.19-.02-.32-.04-.4-.05-.19-.14-.34-.28-.45-.14-.11-.32-.17-.53-.17s-.37.05-.51.16Z" />
                    <path class="cls-18"
                        d="M378.38,214.61c-.18-.08-.32-.17-.42-.3-.1-.12-.15-.26-.15-.42v-.08s.02-.06.06-.06h.3s.06.02.06.06v.05c0,.14.07.26.21.36.14.1.32.15.54.15s.39-.05.53-.15c.13-.1.2-.22.2-.37,0-.1-.03-.19-.1-.26-.07-.07-.15-.12-.24-.16-.09-.04-.24-.08-.43-.14-.23-.07-.42-.13-.57-.2-.15-.07-.28-.16-.38-.28-.1-.12-.15-.27-.15-.45,0-.27.1-.48.31-.64.21-.16.48-.24.82-.24.23,0,.43.04.61.11.18.08.31.18.41.31.1.13.14.28.14.44v.02s-.02.06-.06.06h-.29s-.06-.02-.06-.06v-.02c0-.14-.07-.26-.2-.36s-.32-.14-.55-.14c-.21,0-.38.04-.51.13-.13.09-.19.2-.19.35,0,.14.06.25.19.32.12.08.32.15.58.23.24.07.44.14.59.2.15.06.28.15.39.27s.16.27.16.47c0,.27-.11.49-.32.65-.21.16-.49.24-.84.24-.23,0-.44-.04-.62-.11Z" />
                    <path class="cls-18"
                        d="M382.32,211.9h-.71s-.03,0-.03.03v1.84c0,.2.04.34.13.42.09.08.23.12.42.12h.16s.06.02.06.06v.26s-.02.06-.06.06c-.05,0-.13,0-.23,0-.3,0-.53-.06-.68-.17-.15-.11-.23-.32-.23-.62v-1.98s0-.03-.02-.03h-.38s-.06-.02-.06-.06v-.24s.02-.06.06-.06h.38s.02,0,.02-.03v-.72s.02-.06.06-.06h.31s.06.02.06.06v.72s0,.03.03.03h.71s.06.02.06.06v.24s-.02.06-.06.06Z" />
                    <path class="cls-18"
                        d="M385.21,211.77c.2.19.31.45.31.76v2.1s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-.26s0-.01,0-.02c0,0-.01,0-.02,0-.1.12-.24.22-.41.28-.17.06-.35.1-.56.1-.3,0-.54-.08-.74-.22-.2-.15-.3-.38-.3-.69s.11-.56.34-.74c.23-.18.54-.27.95-.27h.72s.03,0,.03-.03v-.24c0-.21-.06-.38-.18-.5-.12-.12-.3-.18-.55-.18-.2,0-.36.04-.49.12s-.2.18-.23.32c-.01.04-.04.06-.08.06l-.34-.04s-.07-.03-.06-.05c.03-.23.16-.42.38-.57.22-.15.49-.22.81-.22.38,0,.66.1.87.29ZM384.8,214.18c.18-.12.27-.29.27-.5v-.49s0-.03-.03-.03h-.66c-.27,0-.5.06-.66.17-.17.11-.25.27-.25.47,0,.18.06.32.19.42s.29.14.5.14c.25,0,.46-.06.64-.18Z" />
                    <path class="cls-18"
                        d="M390.38,211.77c.18.19.27.44.27.77v2.09s-.02.06-.06.06h-.31s-.06-.02-.06-.06v-2.01c0-.23-.07-.41-.2-.55-.13-.14-.3-.2-.52-.2s-.4.07-.53.2c-.14.13-.2.31-.2.54v2.02s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-2.01c0-.23-.06-.41-.19-.55-.13-.14-.3-.2-.52-.2s-.4.07-.53.2c-.13.13-.2.31-.2.54v2.02s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-3.04s.02-.06.06-.06h.32s.06.02.06.06v.25s0,.01,0,.02.01,0,.02,0c.09-.12.21-.21.35-.27s.3-.09.48-.09c.22,0,.4.04.56.13.15.09.27.21.35.38,0,.02.02.02.03,0,.09-.17.22-.3.39-.38.17-.08.36-.13.57-.13.31,0,.56.09.74.28Z" />
                    <path class="cls-18"
                        d="M391.99,214.51c-.22-.15-.37-.37-.45-.64-.06-.19-.09-.45-.09-.77s.03-.57.09-.76c.08-.27.23-.48.44-.63s.48-.23.79-.23.55.08.77.23c.21.15.36.36.44.62.06.18.09.44.09.77s-.03.59-.09.77c-.08.27-.23.48-.44.64-.21.15-.47.23-.77.23s-.56-.08-.77-.23ZM393.28,214.19c.14-.11.24-.26.29-.45.04-.15.06-.36.06-.63s-.02-.49-.05-.63c-.05-.19-.15-.34-.29-.45s-.32-.17-.52-.17-.38.06-.52.17c-.14.11-.24.26-.29.45-.03.15-.05.36-.05.63s.02.49.05.63c.05.19.14.34.28.45.14.11.32.17.53.17s.37-.05.51-.17Z" />
                    <path class="cls-18"
                        d="M395.38,214.61c-.18-.08-.32-.17-.42-.3-.1-.12-.15-.26-.15-.42v-.08s.02-.06.06-.06h.3s.06.02.06.06v.05c0,.14.07.26.21.36.14.1.32.15.54.15s.39-.05.53-.15c.13-.1.2-.22.2-.37,0-.1-.03-.19-.1-.26-.07-.07-.15-.12-.24-.16-.09-.04-.24-.08-.43-.14-.23-.07-.42-.13-.57-.2-.15-.07-.28-.16-.38-.28-.1-.12-.15-.27-.15-.45,0-.27.1-.48.31-.64.21-.16.48-.24.82-.24.23,0,.43.04.61.11.18.08.31.18.41.31.1.13.14.28.14.44v.02s-.02.06-.06.06h-.29s-.06-.02-.06-.06v-.02c0-.14-.07-.26-.2-.36s-.32-.14-.55-.14c-.21,0-.38.04-.51.13-.13.09-.19.2-.19.35,0,.14.06.25.19.32.12.08.32.15.58.23.24.07.44.14.59.2.15.06.28.15.39.27s.16.27.16.47c0,.27-.11.49-.32.65-.21.16-.49.24-.84.24-.23,0-.44-.04-.62-.11Z" />
                    <path class="cls-18"
                        d="M399.64,214.51c-.21-.16-.36-.37-.44-.63-.05-.18-.08-.44-.08-.78,0-.3.03-.55.08-.76.08-.26.22-.47.44-.62s.47-.23.77-.23.57.08.79.23.37.35.43.58c.02.08.03.14.04.19h0s-.02.06-.06.07l-.31.04h-.01s-.05-.02-.06-.06l-.02-.09c-.04-.16-.13-.29-.28-.4-.15-.11-.32-.17-.53-.17s-.38.06-.52.17-.23.26-.27.44c-.04.16-.06.37-.06.63,0,.28.02.49.06.64.04.19.13.34.27.45s.31.17.52.17.38-.05.53-.16c.15-.11.24-.24.28-.41v-.05s.04-.06.08-.05l.31.05s.06.03.06.07l-.02.12c-.06.24-.21.44-.43.59-.22.15-.49.22-.79.22s-.56-.08-.77-.23Z" />
                    <path class="cls-18"
                        d="M404.44,211.77c.2.19.31.45.31.76v2.1s-.02.06-.06.06h-.32s-.06-.02-.06-.06v-.26s0-.01,0-.02c0,0-.01,0-.02,0-.1.12-.24.22-.41.28-.17.06-.35.1-.56.1-.3,0-.54-.08-.74-.22-.2-.15-.3-.38-.3-.69s.11-.56.34-.74c.23-.18.54-.27.95-.27h.72s.03,0,.03-.03v-.24c0-.21-.06-.38-.18-.5-.12-.12-.3-.18-.55-.18-.2,0-.36.04-.49.12s-.2.18-.23.32c-.01.04-.04.06-.08.06l-.34-.04s-.07-.03-.06-.05c.03-.23.16-.42.38-.57.22-.15.49-.22.81-.22.38,0,.66.1.87.29ZM404.03,214.18c.18-.12.27-.29.27-.5v-.49s0-.03-.03-.03h-.66c-.27,0-.5.06-.66.17-.17.11-.25.27-.25.47,0,.18.06.32.19.42s.29.14.5.14c.25,0,.46-.06.64-.18Z" />
                    <path class="cls-18"
                        d="M406.08,214.61c-.18-.08-.32-.17-.42-.3-.1-.12-.15-.26-.15-.42v-.08s.02-.06.06-.06h.3s.06.02.06.06v.05c0,.14.07.26.21.36.14.1.32.15.54.15s.39-.05.53-.15c.13-.1.2-.22.2-.37,0-.1-.03-.19-.1-.26-.07-.07-.15-.12-.24-.16-.09-.04-.24-.08-.43-.14-.23-.07-.42-.13-.57-.2-.15-.07-.28-.16-.38-.28-.1-.12-.15-.27-.15-.45,0-.27.1-.48.31-.64.21-.16.48-.24.82-.24.23,0,.43.04.61.11.18.08.31.18.41.31.1.13.14.28.14.44v.02s-.02.06-.06.06h-.29s-.06-.02-.06-.06v-.02c0-.14-.07-.26-.2-.36s-.32-.14-.55-.14c-.21,0-.38.04-.51.13-.13.09-.19.2-.19.35,0,.14.06.25.19.32.12.08.32.15.58.23.24.07.44.14.59.2.15.06.28.15.39.27s.16.27.16.47c0,.27-.11.49-.32.65-.21.16-.49.24-.84.24-.23,0-.44-.04-.62-.11Z" />
                    <path class="cls-18"
                        d="M408.72,210.86c-.07-.07-.1-.15-.1-.25s.03-.19.1-.25.15-.1.25-.1.18.03.25.1.1.15.1.25-.03.18-.1.25c-.07.07-.15.1-.25.1s-.18-.03-.25-.1ZM408.73,214.63v-3.04s.02-.06.06-.06h.32s.06.02.06.06v3.04s-.02.06-.06.06h-.32s-.06-.02-.06-.06Z" />
                    <path class="cls-18"
                        d="M411.52,214.63v-4.25s.02-.06.06-.06h.32s.06.02.06.06v4.25s-.02.06-.06.06h-.32s-.06-.02-.06-.06Z" />
                    <path class="cls-18"
                        d="M413,210.86c-.07-.07-.1-.15-.1-.25s.03-.19.1-.25.15-.1.25-.1.18.03.25.1.1.15.1.25-.03.18-.1.25c-.07.07-.15.1-.25.1s-.18-.03-.25-.1ZM413.02,214.63v-3.04s.02-.06.06-.06h.32s.06.02.06.06v3.04s-.02.06-.06.06h-.32s-.06-.02-.06-.06Z" />
                    <path class="cls-18"
                        d="M414.96,214.61c-.18-.08-.32-.17-.42-.3-.1-.12-.15-.26-.15-.42v-.08s.02-.06.06-.06h.3s.06.02.06.06v.05c0,.14.07.26.21.36.14.1.32.15.54.15s.39-.05.53-.15c.13-.1.2-.22.2-.37,0-.1-.03-.19-.1-.26-.07-.07-.15-.12-.24-.16-.09-.04-.24-.08-.43-.14-.23-.07-.42-.13-.57-.2-.15-.07-.28-.16-.38-.28-.1-.12-.15-.27-.15-.45,0-.27.1-.48.31-.64.21-.16.48-.24.82-.24.23,0,.43.04.61.11.18.08.31.18.41.31.1.13.14.28.14.44v.02s-.02.06-.06.06h-.29s-.06-.02-.06-.06v-.02c0-.14-.07-.26-.2-.36s-.32-.14-.55-.14c-.21,0-.38.04-.51.13-.13.09-.19.2-.19.35,0,.14.06.25.19.32.12.08.32.15.58.23.24.07.44.14.59.2.15.06.28.15.39.27s.16.27.16.47c0,.27-.11.49-.32.65-.21.16-.49.24-.84.24-.23,0-.44-.04-.62-.11Z" />
                    <path class="cls-18"
                        d="M418.9,211.9h-.71s-.03,0-.03.03v1.84c0,.2.04.34.13.42.09.08.23.12.42.12h.16s.06.02.06.06v.26s-.02.06-.06.06c-.05,0-.13,0-.23,0-.3,0-.53-.06-.68-.17-.15-.11-.23-.32-.23-.62v-1.98s0-.03-.02-.03h-.38s-.06-.02-.06-.06v-.24s.02-.06.06-.06h.38s.02,0,.02-.03v-.72s.02-.06.06-.06h.31s.06.02.06.06v.72s0,.03.03.03h.71s.06.02.06.06v.24s-.02.06-.06.06Z" />
                    <path class="cls-18"
                        d="M420.16,214.51c-.22-.15-.37-.37-.45-.64-.06-.19-.09-.45-.09-.77s.03-.57.09-.76c.08-.27.23-.48.44-.63s.48-.23.79-.23.55.08.77.23c.21.15.36.36.44.62.06.18.09.44.09.77s-.03.59-.09.77c-.08.27-.23.48-.44.64-.21.15-.47.23-.77.23s-.56-.08-.77-.23ZM421.45,214.19c.14-.11.24-.26.29-.45.04-.15.06-.36.06-.63s-.02-.49-.05-.63c-.05-.19-.15-.34-.29-.45s-.32-.17-.52-.17-.38.06-.52.17c-.14.11-.24.26-.29.45-.03.15-.05.36-.05.63s.02.49.05.63c.05.19.14.34.28.45.14.11.32.17.53.17s.37-.05.51-.17Z" />
                    <path class="cls-18"
                        d="M423.55,214.61c-.18-.08-.32-.17-.42-.3-.1-.12-.15-.26-.15-.42v-.08s.02-.06.06-.06h.3s.06.02.06.06v.05c0,.14.07.26.21.36.14.1.32.15.54.15s.39-.05.53-.15c.13-.1.2-.22.2-.37,0-.1-.03-.19-.1-.26-.07-.07-.15-.12-.24-.16-.09-.04-.24-.08-.43-.14-.23-.07-.42-.13-.57-.2-.15-.07-.28-.16-.38-.28-.1-.12-.15-.27-.15-.45,0-.27.1-.48.31-.64.21-.16.48-.24.82-.24.23,0,.43.04.61.11.18.08.31.18.41.31.1.13.14.28.14.44v.02s-.02.06-.06.06h-.29s-.06-.02-.06-.06v-.02c0-.14-.07-.26-.2-.36s-.32-.14-.55-.14c-.21,0-.38.04-.51.13-.13.09-.19.2-.19.35,0,.14.06.25.19.32.12.08.32.15.58.23.24.07.44.14.59.2.15.06.28.15.39.27s.16.27.16.47c0,.27-.11.49-.32.65-.21.16-.49.24-.84.24-.23,0-.44-.04-.62-.11Z" />
                    <path class="cls-18"
                        d="M426.25,214.58c-.06-.07-.1-.15-.1-.25,0-.1.03-.19.1-.25s.15-.1.25-.1.18.03.25.1.1.15.1.25-.03.18-.1.25-.15.1-.25.1-.19-.03-.25-.1Z" />
                </g>
                <g>
                    <path class="cls-18"
                        d="M378.12,116.59h-3.25s-.04.01-.04.04v2.18s.01.04.04.04h2.28c.06,0,.09.03.09.09v.37c0,.06-.03.09-.09.09h-2.28s-.04.01-.04.04v2.22s.01.04.04.04h3.25c.06,0,.09.03.09.09v.37c0,.06-.03.09-.09.09h-3.83c-.06,0-.09-.03-.09-.09v-6.01c0-.06.03-.09.09-.09h3.83c.06,0,.09.03.09.09v.37c0,.06-.03.09-.09.09Z" />
                    <path class="cls-18"
                        d="M379.75,121.85c-.39-.3-.58-.7-.58-1.2v-.27c0-.06.03-.09.09-.09h.43c.06,0,.09.03.09.09v.24c0,.34.14.62.42.83.28.21.67.31,1.18.31.45,0,.8-.1,1.03-.29s.35-.45.35-.76c0-.21-.05-.38-.16-.53-.11-.15-.28-.29-.5-.42-.23-.13-.55-.28-.96-.45-.44-.18-.79-.33-1.03-.46-.24-.13-.44-.3-.59-.51-.15-.21-.23-.47-.23-.79,0-.49.17-.88.52-1.15.34-.27.82-.41,1.42-.41.65,0,1.17.15,1.55.46s.57.71.57,1.22v.19c0,.06-.03.09-.09.09h-.44c-.06,0-.09-.03-.09-.09v-.16c0-.34-.13-.62-.4-.84-.27-.22-.64-.33-1.12-.33-.42,0-.74.08-.96.26-.22.17-.34.42-.34.74,0,.21.05.39.16.53s.27.26.47.36c.2.1.52.24.94.4.43.18.78.34,1.04.49.26.15.47.34.64.56s.24.49.24.81c0,.5-.18.89-.55,1.18-.37.29-.87.44-1.52.44s-1.19-.15-1.58-.45Z" />
                    <path class="cls-18"
                        d="M388.34,116.13v.38c0,.06-.03.09-.09.09h-1.73s-.04.01-.04.04v5.51c0,.06-.03.09-.09.09h-.45c-.06,0-.09-.03-.09-.09v-5.51s-.01-.04-.04-.04h-1.66c-.06,0-.09-.03-.09-.09v-.38c0-.06.03-.09.09-.09h4.09c.06,0,.09.03.09.09Z" />
                    <path class="cls-18"
                        d="M392.62,122.16l-.35-1.11s-.02-.03-.04-.03h-2.64s-.03,0-.04.03l-.35,1.11s-.05.07-.11.07h-.48c-.06,0-.09-.03-.07-.1l1.95-6.02s.05-.07.11-.07h.6c.05,0,.09.02.11.07l1.96,6.02v.04s-.02.06-.07.06h-.48c-.05,0-.09-.02-.11-.07ZM389.73,120.49s.02.01.03.01h2.28s.02,0,.03-.01.01-.02,0-.03l-1.15-3.61s-.01-.02-.03-.02-.02,0-.03.02l-1.15,3.61s0,.02,0,.03Z" />
                    <path class="cls-18"
                        d="M399.79,116.51c.31.32.47.74.47,1.25s-.16.92-.48,1.24c-.32.31-.74.47-1.27.47h-1.72s-.04.01-.04.04v2.63c0,.06-.03.09-.09.09h-.45c-.06,0-.09-.03-.09-.09v-6.02c0-.06.03-.09.09-.09h2.32c.52,0,.94.16,1.25.48ZM399.32,118.63c.22-.21.33-.5.33-.85s-.11-.65-.33-.87c-.22-.22-.5-.33-.85-.33h-1.68s-.04.01-.04.04v2.3s.01.04.04.04h1.68c.35,0,.63-.11.85-.32Z" />
                    <path class="cls-18"
                        d="M404.78,122.16l-.35-1.11s-.02-.03-.04-.03h-2.64s-.03,0-.04.03l-.35,1.11s-.05.07-.11.07h-.48c-.06,0-.09-.03-.07-.1l1.95-6.02s.05-.07.11-.07h.6c.05,0,.09.02.11.07l1.96,6.02v.04s-.02.06-.07.06h-.48c-.05,0-.09-.02-.11-.07ZM401.89,120.49s.02.01.03.01h2.28s.02,0,.03-.01.01-.02,0-.03l-1.15-3.61s-.01-.02-.03-.02-.02,0-.03.02l-1.15,3.61s0,.02,0,.03ZM402.69,115.43s0-.05,0-.08l.53-.96s.05-.06.11-.06h.4s.06.01.07.03,0,.05-.01.08l-.6.96s-.07.06-.11.06h-.32s-.06-.01-.07-.03Z" />
                    <path class="cls-18"
                        d="M407.27,122.06c-.31-.16-.56-.39-.73-.69-.17-.29-.26-.63-.26-1.02v-2.44c0-.38.09-.72.26-1.02.17-.29.42-.52.73-.69s.67-.24,1.09-.24.77.08,1.08.24.56.38.73.67c.17.29.26.61.26.97v.12c0,.06-.03.09-.09.09h-.45c-.06,0-.09-.03-.09-.09v-.11c0-.39-.13-.72-.39-.96-.26-.25-.61-.37-1.05-.37s-.79.13-1.05.38c-.26.25-.4.59-.4,1v2.47c0,.41.14.75.41,1,.27.25.63.38,1.07.38s.77-.12,1.03-.35c.26-.24.38-.55.38-.95v-.8s-.01-.04-.04-.04h-1.3c-.06,0-.09-.03-.09-.09v-.37c0-.06.03-.09.09-.09h1.87c.06,0,.09.03.09.09v1.14c0,.63-.19,1.12-.56,1.48-.37.35-.88.53-1.51.53-.41,0-.77-.08-1.09-.24Z" />
                    <path class="cls-18"
                        d="M411.9,122.14v-6.01c0-.06.03-.09.09-.09h.45c.06,0,.09.03.09.09v6.01c0,.06-.03.09-.09.09h-.45c-.06,0-.09-.03-.09-.09Z" />
                    <path class="cls-18"
                        d="M417.96,116.04h.45c.06,0,.09.03.09.09v6.01c0,.06-.03.09-.09.09h-.44s-.08-.02-.11-.06l-3.01-4.91s-.01-.03-.03-.02-.02.01-.02.03v4.87c0,.06-.02.09-.08.09h-.45c-.06,0-.09-.03-.09-.09v-6.01c0-.06.03-.09.09-.09h.44s.08.02.11.06l3.01,4.91s.01.03.03.02c.01,0,.02-.01.02-.03v-4.87c0-.06.02-.09.08-.09Z" />
                    <path class="cls-18"
                        d="M423.72,122.16l-.35-1.11s-.02-.03-.04-.03h-2.64s-.03,0-.04.03l-.35,1.11s-.05.07-.11.07h-.48c-.06,0-.09-.03-.07-.1l1.95-6.02s.05-.07.11-.07h.6c.05,0,.09.02.11.07l1.96,6.02v.04s-.02.06-.07.06h-.48c-.05,0-.09-.02-.11-.07ZM420.83,120.49s.02.01.03.01h2.28s.02,0,.03-.01.01-.02,0-.03l-1.15-3.61s-.01-.02-.03-.02-.02,0-.03.02l-1.15,3.61s0,.02,0,.03Z" />
                    <path class="cls-18"
                        d="M285.42,131.05s-.08.05-.15.05h-4.01s-.07.02-.07.07v1.26s.02.07.07.07h2.48c.06,0,.11.02.15.05.04.04.05.08.05.15v1.74c0,.06-.02.11-.05.15s-.08.05-.15.05h-2.48s-.07.02-.07.07v1.4s.02.07.07.07h4.01c.06,0,.11.02.15.05.04.04.05.08.05.15v1.76c0,.06-.02.11-.05.15-.04.04-.08.05-.15.05h-6.4c-.06,0-.11-.02-.15-.05-.04-.04-.05-.08-.05-.15v-8.99c0-.06.02-.11.05-.15s.08-.05.15-.05h6.4c.06,0,.11.02.15.05.04.04.05.09.05.15v1.76c0,.06-.02.11-.05.15Z" />
                    <path class="cls-18"
                        d="M287.9,138.07c-.56-.25-1-.6-1.31-1.05s-.46-.98-.46-1.58v-.19c0-.06.02-.11.05-.15.04-.04.08-.05.15-.05h2.08c.06,0,.11.02.15.05s.05.08.05.15v.08c0,.25.13.47.4.67.26.19.61.29,1.05.29.33,0,.57-.07.71-.21.14-.14.21-.29.21-.46,0-.2-.09-.34-.27-.44-.18-.1-.51-.22-.98-.36l-.39-.11c-.89-.27-1.63-.63-2.23-1.07-.6-.45-.9-1.1-.9-1.95,0-.56.15-1.06.46-1.5.3-.43.72-.77,1.25-1s1.13-.35,1.79-.35c.7,0,1.33.13,1.9.39.57.26,1.01.63,1.34,1.1.33.47.49,1.02.49,1.62v.12c0,.06-.02.11-.05.15-.04.04-.09.05-.15.05h-2.08c-.06,0-.11-.02-.15-.05s-.05-.08-.05-.15h0c0-.28-.12-.53-.36-.75-.24-.22-.57-.33-.98-.33-.29,0-.51.06-.68.17-.17.12-.25.28-.25.48,0,.16.06.3.18.41s.31.22.56.32c.25.1.64.23,1.15.4.07.03.39.12.95.29.56.17,1.03.46,1.4.9.38.43.56.96.56,1.58s-.15,1.12-.46,1.55c-.3.43-.73.77-1.27,1-.55.23-1.18.35-1.91.35s-1.41-.12-1.97-.37Z" />
                    <path class="cls-18"
                        d="M301.57,128.99s.05.09.05.15v1.77c0,.06-.02.11-.05.15s-.08.05-.15.05h-2.25s-.07.02-.07.07v6.95c0,.06-.02.11-.05.15-.04.04-.08.05-.15.05h-2.12c-.06,0-.11-.02-.15-.05s-.05-.08-.05-.15v-6.95s-.02-.07-.07-.07h-2.19c-.06,0-.11-.02-.15-.05-.04-.04-.05-.08-.05-.15v-1.77c0-.06.02-.11.05-.15.04-.04.08-.05.15-.05h7.1c.06,0,.11.02.15.05Z" />
                    <path class="cls-18"
                        d="M307.5,138.16l-.29-1.05s-.04-.05-.07-.05h-2.84s-.05.02-.07.05l-.29,1.05c-.03.12-.1.17-.23.17h-2.29c-.16,0-.22-.08-.17-.23l2.83-9.01c.04-.11.11-.16.23-.16h2.84c.12,0,.19.05.23.16l2.83,9.01s.01.04.01.08c0,.1-.06.15-.19.15h-2.29c-.12,0-.2-.06-.23-.17ZM304.61,128.07s-.03-.11,0-.17l.66-1.23c.04-.1.12-.15.24-.15h1.81c.11,0,.16.04.16.12,0,.04-.01.08-.04.12l-.94,1.26c-.05.08-.13.12-.24.12h-1.5c-.07,0-.12-.03-.15-.07ZM304.87,135.13h1.68c.05,0,.07-.03.05-.08l-.86-3.01s-.02-.04-.04-.04c-.02,0-.03.01-.04.04l-.84,3.01c0,.05,0,.08.05.08Z" />
                    <path class="cls-18"
                        d="M320.46,131.05s-.08.05-.15.05h-4.01s-.07.02-.07.07v1.26s.02.07.07.07h2.48c.06,0,.11.02.15.05.04.04.05.08.05.15v1.74c0,.06-.02.11-.05.15s-.08.05-.15.05h-2.48s-.07.02-.07.07v1.4s.02.07.07.07h4.01c.06,0,.11.02.15.05.04.04.05.08.05.15v1.76c0,.06-.02.11-.05.15-.04.04-.08.05-.15.05h-6.4c-.06,0-.11-.02-.15-.05-.04-.04-.05-.08-.05-.15v-8.99c0-.06.02-.11.05-.15s.08-.05.15-.05h6.4c.06,0,.11.02.15.05.04.04.05.09.05.15v1.76c0,.06-.02.11-.05.15Z" />
                    <path class="cls-18"
                        d="M326.68,128.99s.08-.05.15-.05h2.12c.06,0,.11.02.15.05s.05.09.05.15v8.99c0,.06-.02.11-.05.15-.04.04-.08.05-.15.05h-2.03c-.11,0-.19-.04-.24-.13l-2.74-4.56s-.04-.04-.05-.03c-.02,0-.03.03-.03.06l.03,4.47c0,.06-.02.11-.05.15s-.08.05-.15.05h-2.12c-.06,0-.11-.02-.15-.05-.04-.04-.05-.08-.05-.15v-8.99c0-.06.02-.11.05-.15s.08-.05.15-.05h2.03c.11,0,.19.04.24.13l2.72,4.56s.04.04.05.03c.02,0,.03-.02.03-.06v-4.47c-.01-.06,0-.11.04-.15Z" />
                    <path class="cls-18"
                        d="M334.52,138.01c-.56-.29-.99-.69-1.3-1.21-.31-.52-.46-1.13-.46-1.82v-2.71c0-.68.15-1.28.46-1.8.31-.52.74-.92,1.3-1.21.56-.29,1.21-.43,1.94-.43s1.38.13,1.94.4c.56.27.99.65,1.3,1.13.31.49.46,1.05.46,1.7,0,.08-.07.13-.2.15l-2.12.13h-.04c-.11,0-.16-.05-.16-.15,0-.37-.11-.66-.32-.88-.21-.22-.5-.33-.86-.33s-.64.11-.86.33c-.21.22-.32.51-.32.88v2.89c0,.36.11.65.32.87.21.22.5.33.86.33s.64-.11.86-.33c.21-.22.32-.51.32-.87,0-.05.02-.09.05-.12.04-.03.08-.04.15-.03l2.12.11c.13,0,.2.05.2.13,0,.64-.15,1.21-.46,1.71s-.74.87-1.3,1.15c-.56.27-1.21.41-1.94.41s-1.38-.14-1.94-.43Z" />
                    <path class="cls-18"
                        d="M342.72,138.05c-.57-.3-1.01-.72-1.32-1.26-.31-.54-.47-1.17-.47-1.89v-2.52c0-.7.16-1.32.47-1.85.31-.54.75-.95,1.32-1.25s1.22-.44,1.97-.44,1.41.15,1.98.44c.57.29,1.01.71,1.32,1.25.31.54.47,1.15.47,1.85v2.52c0,.72-.16,1.34-.47,1.89-.31.54-.75.96-1.32,1.26-.57.3-1.23.45-1.98.45s-1.4-.15-1.97-.45ZM345.59,135.97c.23-.25.34-.57.34-.99v-2.63c0-.41-.11-.74-.34-.99-.23-.25-.53-.37-.91-.37s-.67.12-.89.37c-.23.25-.34.58-.34.99v2.63c0,.41.11.74.34.99.23.25.53.37.89.37s.68-.12.91-.37Z" />
                    <path class="cls-18"
                        d="M354.7,128.99s.08-.05.15-.05h2.12c.06,0,.11.02.15.05s.05.09.05.15v8.99c0,.06-.02.11-.05.15-.04.04-.08.05-.15.05h-2.03c-.11,0-.19-.04-.24-.13l-2.74-4.56s-.04-.04-.05-.03c-.02,0-.03.03-.03.06l.03,4.47c0,.06-.02.11-.05.15s-.08.05-.15.05h-2.12c-.06,0-.11-.02-.15-.05-.04-.04-.05-.08-.05-.15v-8.99c0-.06.02-.11.05-.15s.08-.05.15-.05h2.03c.11,0,.19.04.24.13l2.72,4.56s.04.04.05.03c.02,0,.03-.02.03-.06v-4.47c-.01-.06,0-.11.04-.15Z" />
                    <path class="cls-18"
                        d="M359.8,138.07c-.56-.25-1-.6-1.31-1.05s-.46-.98-.46-1.58v-.19c0-.06.02-.11.05-.15.04-.04.08-.05.15-.05h2.08c.06,0,.11.02.15.05s.05.08.05.15v.08c0,.25.13.47.4.67.26.19.61.29,1.05.29.33,0,.57-.07.71-.21.14-.14.21-.29.21-.46,0-.2-.09-.34-.27-.44-.18-.1-.51-.22-.98-.36l-.39-.11c-.89-.27-1.63-.63-2.23-1.07-.6-.45-.9-1.1-.9-1.95,0-.56.15-1.06.46-1.5.3-.43.72-.77,1.25-1s1.13-.35,1.79-.35c.7,0,1.33.13,1.9.39.57.26,1.01.63,1.34,1.1.33.47.49,1.02.49,1.62v.12c0,.06-.02.11-.05.15-.04.04-.09.05-.15.05h-2.08c-.06,0-.11-.02-.15-.05s-.05-.08-.05-.15h0c0-.28-.12-.53-.36-.75-.24-.22-.57-.33-.98-.33-.29,0-.51.06-.68.17-.17.12-.25.28-.25.48,0,.16.06.3.18.41s.31.22.56.32c.25.1.64.23,1.15.4.07.03.39.12.95.29.56.17,1.03.46,1.4.9.38.43.56.96.56,1.58s-.15,1.12-.46,1.55c-.3.43-.73.77-1.27,1-.55.23-1.18.35-1.91.35s-1.41-.12-1.97-.37Z" />
                    <path class="cls-18"
                        d="M373.46,128.99s.05.09.05.15v1.77c0,.06-.02.11-.05.15s-.08.05-.15.05h-2.25s-.07.02-.07.07v6.95c0,.06-.02.11-.05.15-.04.04-.08.05-.15.05h-2.12c-.06,0-.11-.02-.15-.05s-.05-.08-.05-.15v-6.95s-.02-.07-.07-.07h-2.19c-.06,0-.11-.02-.15-.05-.04-.04-.05-.08-.05-.15v-1.77c0-.06.02-.11.05-.15.04-.04.08-.05.15-.05h7.1c.06,0,.11.02.15.05Z" />
                    <path class="cls-18"
                        d="M378.99,138.19l-1.48-3.41s-.04-.05-.08-.05h-.56s-.07.02-.07.07v3.34c0,.06-.02.11-.05.15s-.08.05-.15.05h-2.12c-.06,0-.11-.02-.15-.05-.04-.04-.05-.08-.05-.15v-8.99c0-.06.02-.11.05-.15s.08-.05.15-.05h4.07c.61,0,1.14.13,1.6.38.46.25.82.6,1.07,1.05.25.45.38.97.38,1.56s-.15,1.11-.44,1.55c-.29.44-.69.77-1.2.97-.04.02-.06.05-.04.09l1.71,3.54c.02.05.03.08.03.09,0,.04-.02.08-.05.11-.04.03-.08.04-.13.04h-2.25c-.12,0-.19-.05-.23-.15ZM376.81,131.17v1.57s.02.07.07.07h1.26c.29,0,.52-.08.7-.24.18-.16.27-.36.27-.61s-.09-.47-.27-.62-.41-.24-.7-.24h-1.26s-.07.02-.07.07Z" />
                    <path class="cls-18"
                        d="M384.23,138.03c-.56-.28-.99-.67-1.3-1.18-.3-.51-.46-1.1-.46-1.77v-5.93c0-.06.02-.11.05-.15.04-.04.08-.05.15-.05h2.12c.06,0,.11.02.15.05.04.04.05.09.05.15v5.93c0,.37.1.66.32.88.21.22.49.33.84.33s.62-.11.83-.33.32-.51.32-.87v-5.93c0-.06.02-.11.05-.15s.08-.05.15-.05h2.12c.06,0,.11.02.15.05.04.04.05.09.05.15v5.93c0,.67-.15,1.26-.46,1.77-.3.51-.73.9-1.29,1.18s-1.19.42-1.92.42-1.37-.14-1.93-.42Z" />
                    <path class="cls-18"
                        d="M392.46,138.01c-.56-.29-.99-.69-1.3-1.21-.31-.52-.46-1.13-.46-1.82v-2.71c0-.68.15-1.28.46-1.8.31-.52.74-.92,1.3-1.21.56-.29,1.21-.43,1.94-.43s1.38.13,1.94.4c.56.27.99.65,1.3,1.13.31.49.46,1.05.46,1.7,0,.08-.07.13-.2.15l-2.12.13h-.04c-.11,0-.16-.05-.16-.15,0-.37-.11-.66-.32-.88-.21-.22-.5-.33-.86-.33s-.64.11-.86.33c-.21.22-.32.51-.32.88v2.89c0,.36.11.65.32.87.21.22.5.33.86.33s.64-.11.86-.33c.21-.22.32-.51.32-.87,0-.05.02-.09.05-.12.04-.03.08-.04.15-.03l2.12.11c.13,0,.2.05.2.13,0,.64-.15,1.21-.46,1.71s-.74.87-1.3,1.15c-.56.27-1.21.41-1.94.41s-1.38-.14-1.94-.43Z" />
                    <path class="cls-18"
                        d="M400.63,138.01c-.56-.29-.99-.69-1.3-1.21-.31-.52-.46-1.13-.46-1.82v-2.71c0-.68.15-1.28.46-1.8.31-.52.74-.92,1.3-1.21.56-.29,1.21-.43,1.94-.43s1.38.13,1.94.4c.56.27.99.65,1.3,1.13.31.49.46,1.05.46,1.7,0,.08-.07.13-.2.15l-2.12.13h-.04c-.11,0-.16-.05-.16-.15,0-.37-.11-.66-.32-.88-.21-.22-.5-.33-.86-.33s-.64.11-.86.33c-.21.22-.32.51-.32.88v2.89c0,.36.11.65.32.87.21.22.5.33.86.33s.64-.11.86-.33c.21-.22.32-.51.32-.87,0-.05.02-.09.05-.12.04-.03.08-.04.15-.03l2.12.11c.13,0,.2.05.2.13,0,.64-.15,1.21-.46,1.71s-.74.87-1.3,1.15c-.56.27-1.21.41-1.94.41s-1.38-.14-1.94-.43Z" />
                    <path class="cls-18"
                        d="M407.22,138.28s-.05-.08-.05-.15v-8.99c0-.06.02-.11.05-.15s.08-.05.15-.05h2.12c.06,0,.11.02.15.05.04.04.05.09.05.15v8.99c0,.06-.02.11-.05.15s-.08.05-.15.05h-2.12c-.06,0-.11-.02-.15-.05Z" />
                    <path class="cls-18"
                        d="M412.41,138.05c-.57-.3-1.01-.72-1.32-1.26-.31-.54-.47-1.17-.47-1.89v-2.52c0-.7.16-1.32.47-1.85.31-.54.75-.95,1.32-1.25s1.22-.44,1.97-.44,1.41.15,1.98.44c.57.29,1.01.71,1.32,1.25.31.54.47,1.15.47,1.85v2.52c0,.72-.16,1.34-.47,1.89-.31.54-.75.96-1.32,1.26-.57.3-1.23.45-1.98.45s-1.4-.15-1.97-.45ZM415.28,135.97c.23-.25.34-.57.34-.99v-2.63c0-.41-.11-.74-.34-.99-.23-.25-.53-.37-.91-.37s-.67.12-.89.37c-.23.25-.34.58-.34.99v2.63c0,.41.11.74.34.99.23.25.53.37.89.37s.68-.12.91-.37ZM413.29,128.07s-.03-.11,0-.17l.66-1.23c.04-.1.13-.15.24-.15h1.81c.11,0,.16.04.16.12,0,.04-.01.08-.04.12l-.94,1.26c-.05.08-.13.12-.24.12h-1.5c-.07,0-.12-.03-.15-.07Z" />
                    <path class="cls-18"
                        d="M424.38,128.99s.08-.05.15-.05h2.12c.06,0,.11.02.15.05s.05.09.05.15v8.99c0,.06-.02.11-.05.15-.04.04-.08.05-.15.05h-2.03c-.11,0-.19-.04-.24-.13l-2.74-4.56s-.04-.04-.05-.03c-.02,0-.03.03-.03.06l.03,4.47c0,.06-.02.11-.05.15s-.08.05-.15.05h-2.12c-.06,0-.11-.02-.15-.05-.04-.04-.05-.08-.05-.15v-8.99c0-.06.02-.11.05-.15s.08-.05.15-.05h2.03c.11,0,.19.04.24.13l2.72,4.56s.04.04.05.03c.02,0,.03-.02.03-.06v-4.47c-.01-.06,0-.11.04-.15Z" />
                </g>
                <g>
                    <g>
                        <path class="cls-32"
                            d="M291.54,228.63H5.29c-.29,0-.52-.23-.52-.52s.23-.52.52-.52h286.25c.29,0,.52.23.52.52s-.23.52-.52.52Z" />
                        <path class="cls-32"
                            d="M166,245.73h-56.35c-.29,0-.52-.23-.52-.52s.23-.52.52-.52h56.35c.29,0,.52.23.52.52s-.23.52-.52.52Z" />
                        <path class="cls-32"
                            d="M234.04,245.73h-56.35c-.29,0-.52-.23-.52-.52s.23-.52.52-.52h56.35c.29,0,.52.23.52.52s-.23.52-.52.52Z" />
                        <path class="cls-32"
                            d="M228.36,232.78H84.87c-.29,0-.52-.23-.52-.52s.23-.52.52-.52h143.49c.29,0,.52.23.52.52s-.23.52-.52.52Z" />
                    </g>
                    <g>
                        <g>
                            <path class="cls-7"
                                d="M31.2,177.53l-15.51,47.15,33.72.14-15.68-47.29c-.46-1.38-2.07-1.38-2.53,0Z" />
                            <rect class="cls-31" x="14.16" y="221.68" width="36.47" height="5.41" />
                            <polygon class="cls-42"
                                points="26.3 192.42 38.67 192.42 36.74 186.6 28.21 186.6 26.3 192.42" />
                            <polygon class="cls-42"
                                points="22.14 205.08 42.87 205.08 40.94 199.26 24.05 199.26 22.14 205.08" />
                            <polygon class="cls-42"
                                points="17.97 217.74 47.06 217.74 45.13 211.92 19.89 211.92 17.97 217.74" />
                        </g>
                        <g class="cls-44">
                            <path
                                d="M48.37,221.68l-1.31-3.94h0l-1.44-4.35-2.75-8.31h0l-1.93-5.82h0l-2.27-6.84h0l-.85-2.56-4.09-12.34c-.14-.42-.4-.7-.69-.87,3.35,19.95,5.7,36.57,7.01,50.43h10.58v-5.41h-2.26Z" />
                        </g>
                    </g>
                    <g>
                        <g>
                            <path class="cls-7"
                                d="M253.31,170.09l-15.51,47.15,33.72.14-15.68-47.29c-.46-1.38-2.07-1.38-2.53,0Z" />
                            <rect class="cls-31" x="236.27" y="214.24" width="36.47" height="5.41" />
                            <polygon class="cls-42"
                                points="248.41 184.98 260.78 184.98 258.85 179.16 250.33 179.16 248.41 184.98" />
                            <polygon class="cls-42"
                                points="244.25 197.64 264.98 197.64 263.05 191.82 246.16 191.82 244.25 197.64" />
                            <polygon class="cls-42"
                                points="240.08 210.3 269.18 210.3 267.25 204.48 242 204.48 240.08 210.3" />
                        </g>
                        <g class="cls-44">
                            <path
                                d="M270.49,214.24l-1.31-3.94h0l-1.44-4.35-2.75-8.31h0l-1.93-5.82h0l-2.27-6.84h0l-.85-2.56-4.09-12.34c-.14-.42-.4-.7-.69-.87,3.35,19.95,5.7,36.57,7.01,50.43h10.58v-5.41h-2.26Z" />
                        </g>
                    </g>
                    <g>
                        <g>
                            <path class="cls-24"
                                d="M63.74,226.71h0c-.75,0-1.3-.61-1.24-1.36l8.6-98.25c.07-.75.73-1.36,1.47-1.36h0c.75,0,1.3.61,1.24,1.36l-8.6,98.25c-.07.75-.73,1.36-1.47,1.36Z" />
                            <path class="cls-24"
                                d="M39.5,226.71h0c-.75,0-1.3-.61-1.24-1.36l8.6-98.25c.07-.75.73-1.36,1.47-1.36h0c.75,0,1.3.61,1.24,1.36l-8.6,98.25c-.07.75-.73,1.36-1.47,1.36Z" />
                            <g>
                                <path class="cls-24"
                                    d="M47,140.93l.13-1.54c.03-.38.37-.69.75-.69h22.87c.38,0,.66.31.63.69l-.13,1.54c-.03.38-.37.69-.75.69h-22.87c-.38,0-.66-.31-.63-.69Z" />
                                <path class="cls-24"
                                    d="M45.74,155.36l.13-1.54c.03-.38.37-.69.75-.69h22.87c.38,0,.66.31.63.69l-.13,1.54c-.03.38-.37.69-.75.69h-22.87c-.38,0-.66-.31-.63-.69Z" />
                                <path class="cls-24"
                                    d="M44.48,169.79l.13-1.54c.03-.38.37-.69.75-.69h22.87c.38,0,.66.31.63.69l-.13,1.54c-.03.38-.37.69-.75.69h-22.87c-.38,0-.66-.31-.63-.69Z" />
                                <path class="cls-24"
                                    d="M43.21,184.21l.13-1.54c.03-.38.37-.69.75-.69h22.87c.38,0,.66.31.63.69l-.13,1.54c-.03.38-.37.69-.75.69h-22.87c-.38,0-.66-.31-.63-.69Z" />
                                <path class="cls-24"
                                    d="M41.95,198.64l.13-1.54c.03-.38.37-.69.75-.69h22.87c.38,0,.66.31.63.69l-.13,1.54c-.03.38-.37.69-.75.69h-22.87c-.38,0-.66-.31-.63-.69Z" />
                                <path class="cls-24"
                                    d="M40.69,213.07l.13-1.54c.03-.38.37-.69.75-.69h22.87c.38,0,.66.31.63.69l-.13,1.54c-.03.38-.37.69-.75.69h-22.87c-.38,0-.66-.31-.63-.69Z" />
                            </g>
                        </g>
                        <g>
                            <path class="cls-32"
                                d="M66.41,81.51c-1.24-1.39-3.33-1.74-5.15-1.36-1.82.38-3.44,1.38-5.02,2.37l-.92.95c-1.78.28-3.01,2.2-2.88,4s1.37,3.38,2.9,4.33c2.36,1.46,5.49,1.57,7.95.28s4.15-3.93,4.29-6.7c.07-1.38-.25-2.83-1.17-3.86Z" />
                            <path class="cls-9"
                                d="M70.65,112.48c-3.15,0-6.31-1.02-8.85-2.89-.47-.34-.56-1-.22-1.46.34-.46,1-.57,1.46-.22,2.44,1.8,5.58,2.69,8.6,2.44,3.02-.25,5.97-1.64,8.09-3.81.4-.41,1.07-.42,1.48-.02.41.4.42,1.06.02,1.48-2.46,2.53-5.9,4.14-9.41,4.43-.39.03-.78.05-1.16.05Z" />
                            <path class="cls-43"
                                d="M53.08,117.47l13.58.06,1.21-18.54c0-2.23-1.95-4.04-4.42-4.05l-5.73-.02c-2.52,0-4.56,2.27-4.57,4.54l-.07,18.01Z" />
                            <path class="cls-47"
                                d="M63,111.09v-16.17h.81c2.3-.01,4.17,2.02,4.18,4.53l.06,17.17h0c-2.79,0-5.04-2.48-5.04-5.53Z" />
                            <rect class="cls-42" x="63" y="106.18" width="5.01" height="2.96" />
                            <path class="cls-47"
                                d="M64.1,106.2h2.66c1.82,0,3.29-1.47,3.29-3.29h0c0-1.82-1.47-3.29-3.29-3.29h-2.66v6.59Z" />
                            <g>
                                <rect class="cls-7" x="63.48" y="159.7" width="2.53" height="4.88" />
                                <path class="cls-17"
                                    d="M71.92,167.37c-.02-.62-.28-2.43-1.74-2.43l-2.43-.03-.51-1.55c-.1-.31-.34-.52-.61-.52h-.39c-.1,0-.13.04-.14.14-.06.81-.14,1.2-.63,1.2l-2-.02c-.81,0-.76.42-.77.96,0,0-.02,1.82-.02,2.26h9.23Z" />
                            </g>
                            <g>
                                <rect class="cls-7" x="54.28" y="159.9" width="2.53" height="4.88" />
                                <g>
                                    <path class="cls-32"
                                        d="M57.68,165.47h-2.96v-1.73c0-.4.32-.72.72-.72h1.53c.4,0,.72.32.72.72v1.73Z" />
                                    <path class="cls-32"
                                        d="M58.37,167.37h-4.73v-1.93c0-.37.3-.67.67-.67h2.67c.77,0,1.39.62,1.39,1.4v1.2Z" />
                                </g>
                            </g>
                            <polygon class="cls-17"
                                points="66.53 161.59 62.41 161.58 61.91 124.81 66.67 122.23 66.53 161.59" />
                            <path class="cls-32"
                                d="M53.9,161.48l-1.78-37.28c-.27-4.22.96-6.72.96-6.72l13.58.06.13,3.5c.16,3.78-3.29,5.12-6.32,5.1h-1.57s-1.87,35.43-1.87,35.43l-3.13-.08Z" />
                            <path class="cls-9"
                                d="M60.84,90.16h.03c1.15,0,2.08.93,2.08,2.08v2.91c0,1.14-.93,2.07-2.07,2.07h-.03c-1.15,0-2.08-.93-2.08-2.08v-2.91c0-1.14.93-2.07,2.07-2.07Z"
                                transform="translate(-.99 .65) rotate(-.61)" />
                            <path class="cls-9"
                                d="M64.69,85.26l.48-.05c.71-.07,1.33.45,1.4,1.16l.06.61c.07.71-.45,1.33-1.16,1.4l-.48.05-.3-3.17Z" />
                            <path class="cls-32" d="M55.62,82.27h2.5v7.95h-.33c-1.2,0-2.17-.97-2.17-2.17v-5.78h0Z"
                                transform="translate(-7.93 5.79) rotate(-5.45)" />
                            <path class="cls-30"
                                d="M60.5,79.49c-2.5.24-4.34,2.38-4.11,4.76.27,2.81.25,7.7,3.75,8.62,3.05.8,6.25-.5,6.04-3.86-.12-1.87-.79-6.24-.96-8.09-.22-2.38-2.21-1.68-4.71-1.44Z" />
                            <g>
                                <path class="cls-32"
                                    d="M63.65,86.37c.02.25.25.43.5.41.25-.02.43-.25.41-.5-.02-.25-.25-.43-.5-.41-.25.02-.43.25-.41.5Z" />
                                <path class="cls-32"
                                    d="M60.08,86.71c.02.25.25.43.5.41.25-.02.43-.25.41-.5s-.25-.43-.5-.41c-.25.02-.43.25-.41.5Z" />
                            </g>
                            <g>
                                <path class="cls-32"
                                    d="M61.23,84.66l-1.69.16c-.12.01-.22-.07-.23-.19-.01-.12.07-.22.19-.23l1.69-.16c.12-.01.22.07.23.19.01.12-.07.22-.19.23Z" />
                                <path class="cls-32"
                                    d="M64.68,84.33l-1.69.16c-.12.01-.22-.07-.23-.19-.01-.12.07-.22.19-.23l1.69-.16c.12-.01.22.07.23.19.01.12-.07.22-.19.23Z" />
                            </g>
                            <path class="cls-29"
                                d="M59.96,88.65c.31.82,1.43,1.56,2.68,1.44s2.22-1.05,2.36-1.92l-5.05.48Z" />
                            <path class="cls-9"
                                d="M62.67,88.01c-.18,0-.32-.05-.33-.05-.06-.02-.09-.08-.07-.14.02-.06.08-.09.14-.07,0,0,.4.14.62-.1.09-.1-.16-.72-.52-1.3-.03-.05-.02-.12.04-.16.05-.03.12-.02.16.04.24.38.76,1.29.5,1.57-.16.17-.36.21-.53.21Z" />
                            <path class="cls-30"
                                d="M56.73,86.02l-.48.05c-.71.07-1.22.69-1.16,1.4l.06.61c.07.71.69,1.22,1.4,1.16l.48-.05-.3-3.17Z" />
                            <path class="cls-47"
                                d="M58.6,111.09v-16.17h-.88c-2.52-.01-4.56,2.02-4.57,4.53l-.06,17.17h0c3.05,0,5.52-2.48,5.52-5.53Z" />
                            <rect class="cls-42" x="53.08" y="106.18" width="5.53" height="2.96" />
                            <path class="cls-32"
                                d="M65.15,80.28s.01-.06.02-.09c-.03-.04-.06-.08-.09-.12-.24-.59-.72-1.09-1.29-1.37-.28-.13-.57-.22-.87-.26-3.18-1.2-7.92.53-8.61,7.08,0,.54-.04,1.53-.08,2.03,6.44.42,3.75-2.04,4.24-4.03,1.75.85,3.98.67,5.54-.52.6-.46,1.11-1.08,1.22-1.82.05-.3.01-.6-.07-.89Z" />
                            <g>
                                <g class="cls-44">
                                    <path d="M66.29,81.38l-11.56,2.24c-.86.34-1.35.98-1.35.98l13.17-2.92-.27-.3Z" />
                                </g>
                                <path class="cls-47"
                                    d="M59.36,75.63c-3.28.64-5.43,3.82-4.79,7.1l11.89-2.31c-.64-3.28-3.82-5.43-7.1-4.79Z" />
                                <path class="cls-3"
                                    d="M66.73,81.29l-12.09,2.35c-.25.05-.49-.11-.53-.36h0c-.05-.25.11-.49.36-.53l12.09-2.35c.25-.05.49.11.53.36h0c.05.25-.11.49-.36.53Z" />
                                <path class="cls-3"
                                    d="M58.34,75.92c.26.06.55.09.66.13.46.15.91.37,1.31.65.84.58,1.51,1.42,1.88,2.38.07.18.13.37.24.53.1.16.26.31.46.34.28.05.55-.14.68-.4.13-.25.13-.55.12-.83-.06-.95-.41-1.89-1.03-2.62-.1-.11-.2-.22-.31-.32-.93-.3-1.95-.37-2.98-.17-.35.07-.69.17-1.02.3Z" />
                            </g>
                        </g>
                        <g>
                            <path class="cls-31"
                                d="M57.34,226.71h0c.75,0,1.31-.61,1.24-1.36l-8.62-98.56c-.07-.75-.73-1.36-1.48-1.36h0c-.75,0-1.31.61-1.24,1.36l8.62,98.56c.07.75.73,1.36,1.48,1.36Z" />
                            <path class="cls-31"
                                d="M81.66,226.71h0c.75,0,1.31-.61,1.24-1.36l-8.62-98.56c-.07-.75-.73-1.36-1.48-1.36h0c-.75,0-1.31.61-1.24,1.36l8.62,98.56c.07.75.73,1.36,1.48,1.36Z" />
                            <g>
                                <path class="cls-31"
                                    d="M74.14,140.66l-.14-1.55c-.03-.38-.37-.69-.75-.69h-22.94c-.38,0-.66.31-.63.69l.14,1.55c.03.38.37.69.75.69h22.94c.38,0,.66-.31.63-.69Z" />
                                <path class="cls-31"
                                    d="M75.4,155.13l-.14-1.55c-.03-.38-.37-.69-.75-.69h-22.94c-.38,0-.66.31-.63.69l.14,1.55c.03.38.37.69.75.69h22.94c.38,0,.66-.31.63-.69Z" />
                                <path class="cls-31"
                                    d="M76.67,169.61l-.14-1.55c-.03-.38-.37-.69-.75-.69h-22.94c-.38,0-.66.31-.63.69l.14,1.55c.03.38.37.69.75.69h22.94c.38,0,.66-.31.63-.69Z" />
                                <path class="cls-31"
                                    d="M77.93,184.08l-.14-1.55c-.03-.38-.37-.69-.75-.69h-22.94c-.38,0-.66.31-.63.69l.14,1.55c.03.38.37.69.75.69h22.94c.38,0,.66-.31.63-.69Z" />
                                <path class="cls-31"
                                    d="M79.2,198.55l-.14-1.55c-.03-.38-.37-.69-.75-.69h-22.94c-.38,0-.66.31-.63.69l.14,1.55c.03.38.37.69.75.69h22.94c.38,0,.66-.31.63-.69Z" />
                                <path class="cls-31"
                                    d="M80.47,213.02l-.14-1.55c-.03-.38-.37-.69-.75-.69h-22.94c-.38,0-.66.31-.63.69l.14,1.55c.03.38.37.69.75.69h22.94c.38,0,.66-.31.63-.69Z" />
                            </g>
                        </g>
                    </g>
                    <g>
                        <g>
                            <path class="cls-31"
                                d="M81.22,227.57h134.58l-12.52-102.47H57.94l11.22,91.82c.75,6.16,5.84,10.65,12.06,10.65Z" />
                            <polygon class="cls-14"
                                points="57.94 125.1 69.46 219.39 72.95 219.39 61.42 125.1 57.94 125.1" />
                        </g>
                        <path class="cls-31"
                            d="M280.5,227.34H80.48c-5.56,0-10.32-4.02-11.28-9.52l-.5-4.21h211.8v13.74Z" />
                        <path class="cls-14" d="M143.77,213.84h-74.98l1.3,13.74h73.69v-13.74Z" />
                        <polygon class="cls-50"
                            points="68.92 215.97 280.5 215.97 280.5 213.61 68.78 213.84 68.92 215.97" />
                        <polygon class="cls-7"
                            points="209.98 208.97 199.79 129.91 67.43 129.91 77.14 208.97 209.98 208.97" />
                        <path class="cls-50"
                            d="M135.74,127.85l-.07-.58c-.04-.37-.35-.63-.72-.63h-1.96c-.47,0-.85.43-.8.9l.07.58c.04.37.35.63.72.63h1.96c.47,0,.85-.43.8-.9Z" />
                        <polygon class="cls-18"
                            points="91.94 219.39 82.98 219.39 83.44 222.02 92.4 222.02 91.94 219.39" />
                        <polygon class="cls-18"
                            points="104.35 219.39 95.39 219.39 95.85 222.02 104.81 222.02 104.35 219.39" />
                    </g>
                    <g>
                        <g>
                            <path class="cls-7"
                                d="M204.35,195.39l-15.51,47.15,33.72.14-15.68-47.29c-.46-1.38-2.07-1.38-2.53,0Z" />
                            <rect class="cls-31" x="187.31" y="239.54" width="36.47" height="5.41" />
                            <polygon class="cls-42"
                                points="199.45 210.27 211.81 210.27 209.88 204.45 201.36 204.45 199.45 210.27" />
                            <polygon class="cls-42"
                                points="195.28 222.93 216.01 222.93 214.08 217.12 197.2 217.12 195.28 222.93" />
                            <polygon class="cls-42"
                                points="191.12 235.59 220.21 235.59 218.28 229.78 193.03 229.78 191.12 235.59" />
                        </g>
                        <g class="cls-44">
                            <path
                                d="M221.52,239.54l-1.31-3.94h0l-1.44-4.35-2.75-8.31h0l-1.93-5.82h0l-2.27-6.84h0l-.85-2.56-4.09-12.34c-.14-.42-.4-.7-.69-.87,3.35,19.95,5.7,36.57,7.01,50.43h10.58v-5.41h-2.26Z" />
                        </g>
                    </g>
                    <g>
                        <path class="cls-27"
                            d="M228.35,151.14c-3.31,0-6.64-1.15-9.24-3.2-2.77-2.19-4.73-5.44-5.38-8.91-.11-.57.27-1.11.84-1.22.57-.1,1.11.27,1.22.83.56,2.98,2.24,5.77,4.62,7.65,2.38,1.88,5.49,2.88,8.51,2.73.6-.02,1.07.42,1.09,1,.03.58-.42,1.07-.99,1.09-.22.01-.45.02-.67.02Z" />
                        <g>
                            <rect class="cls-7" x="229.9" y="205.94" width="2.53" height="4.88"
                                transform="translate(462.33 416.76) rotate(180)" />
                            <path class="cls-17"
                                d="M223.99,213.61c.02-.62.28-2.43,1.74-2.43l2.43-.03.51-1.55c.1-.31.34-.52.61-.52h.39c.1,0,.13.04.14.14.06.81.14,1.2.63,1.2l2-.02c.81,0,.76.42.77.96,0,0,.02,1.82.02,2.26h-9.23Z" />
                        </g>
                        <g>
                            <rect class="cls-7" x="239.53" y="206.13" width="2.53" height="4.88"
                                transform="translate(481.59 417.14) rotate(180)" />
                            <g>
                                <path class="cls-32"
                                    d="M238.66,211.71h2.96v-1.73c0-.4-.32-.72-.72-.72h-1.53c-.4,0-.72.32-.72.72v1.73Z" />
                                <path class="cls-32"
                                    d="M237.98,213.61h4.73v-1.93c0-.37-.3-.67-.67-.67h-2.67c-.77,0-1.39.62-1.39,1.4v1.2Z" />
                            </g>
                        </g>
                        <path class="cls-43"
                            d="M242.83,166.58l-14.76.06-.03-20.53c0-2.47,1.95-4.47,4.42-4.48l5.73-.02c2.52,0,4.56,2.02,4.57,4.54l.07,20.44Z" />
                        <rect class="cls-17" x="229.32" y="171.51" width="4.12" height="36.77"
                            transform="translate(463.47 378.91) rotate(179.78)" />
                        <path class="cls-32"
                            d="M242.85,172.79l-12.77.05c-1.03,0-1.87-.83-1.88-1.86l-.02-4.34,14.64-.06.02,6.21Z" />
                        <path class="cls-32"
                            d="M238.88,208.25c1.37,0,2.74-.01,4.12-.02-.04-12.26-.09-24.51-.14-36.77-1.37,0-2.74.01-4.12.02.05,12.26.09,24.51.14,36.77Z" />
                        <path class="cls-27"
                            d="M233.6,138.29h2.91c1.15,0,2.08.93,2.08,2.08v.03c0,1.14-.93,2.07-2.07,2.07h-2.91c-1.15,0-2.08-.93-2.08-2.08v-.03c0-1.14.93-2.07,2.07-2.07Z"
                            transform="translate(92.2 373.94) rotate(-89.39)" />
                        <g>
                            <path class="cls-27"
                                d="M231.22,131.95l-.48-.05c-.71-.07-1.33.45-1.4,1.16l-.06.61c-.07.71.45,1.33,1.16,1.4l.48.05.3-3.17Z" />
                            <path class="cls-32" d="M239.96,128.97h.33v7.95h-2.5v-5.78c0-1.2.97-2.17,2.17-2.17Z"
                                transform="translate(464.38 287.97) rotate(-174.55)" />
                            <path class="cls-19"
                                d="M235.41,126.19c2.5.24,4.33,2.48,4.09,4.98-.08.87-.45,6.69-2.24,8.28-1.09.97-2.82,1.09-4.2,1-1.53-.1-3.27-.51-3.42-2.3-.12-1.33.84-8.29,1.05-10.43.24-2.5,2.22-1.78,4.72-1.54Z" />
                            <g>
                                <path class="cls-32"
                                    d="M232.26,133.06c-.02.25-.25.43-.5.41-.25-.02-.43-.25-.41-.5.02-.25.25-.43.5-.41.25.02.43.25.41.5Z" />
                                <path class="cls-32"
                                    d="M235.83,133.4c-.02.25-.25.43-.5.41-.25-.02-.43-.25-.41-.5s.25-.43.5-.41c.25.02.43.25.41.5Z" />
                            </g>
                            <g>
                                <path class="cls-32"
                                    d="M234.68,131.19l1.69.16c.12.01.22-.07.23-.19.01-.12-.07-.22-.19-.23l-1.69-.16c-.12-.01-.22.07-.23.19-.01.12.07.22.19.23Z" />
                                <path class="cls-32"
                                    d="M231.23,130.85l1.69.16c.12.01.22-.07.23-.19.01-.12-.07-.22-.19-.23l-1.69-.16c-.12-.01-.22.07-.23.19-.01.12.07.22.19.23Z" />
                            </g>
                            <path class="cls-29"
                                d="M235.95,135.35c-.31.82-1.43,1.56-2.68,1.44-1.25-.12-2.22-1.05-2.36-1.92l5.05.48Z" />
                            <path class="cls-27"
                                d="M233.24,134.71c-.17,0-.37-.04-.53-.21-.26-.28.26-1.19.5-1.57.03-.05.1-.07.16-.04.05.03.07.1.04.16-.36.58-.61,1.2-.52,1.3.22.24.62.1.62.1.06-.02.12.01.14.07.02.06-.01.12-.07.14-.01,0-.15.05-.33.05Z" />
                            <path class="cls-19"
                                d="M239.19,132.71l.48.05c.71.07,1.22.69,1.16,1.4l-.06.61c-.07.71-.69,1.22-1.4,1.16l-.48-.05.3-3.17Z" />
                            <path class="cls-32"
                                d="M237.57,124.89c-1.5-.52-3.09-.65-4.67-.64-.49,0,5.73,1.51,5.26,1.65-2.36.7-8.63-.53-8.11,1.94.46,2.18,4.76,2.4,6.66,1.67.6,5.35,2.42,3.73,2.95,3.24v-2.91c.07-.06.1-.09.17-.15l.77.07.1-1.01c.19-2.04-1.28-3.23-3.13-3.88ZM238.06,128.52c.07-.06.1-.06.09.04l-.09-.04Z" />
                        </g>
                        <g>
                            <g class="cls-44">
                                <polygon
                                    points="230.82 125.61 240.7 128.19 240.52 130.85 230.12 128.12 229.97 127.6 230.82 125.61" />
                            </g>
                            <path class="cls-47"
                                d="M236.62,122.03c3.28.64,5.43,3.82,4.79,7.1l-11.89-2.31c.64-3.28,3.82-5.43,7.1-4.79Z" />
                            <path class="cls-3"
                                d="M229.24,127.69l12.09,2.35c.25.05.49-.11.53-.36h0c.05-.25-.11-.49-.36-.53l-12.09-2.35c-.25-.05-.49.11-.53.36h0c-.05.25.11.49.36.53Z" />
                            <path class="cls-3"
                                d="M237.63,122.32c-.26.06-.55.09-.66.13-.46.15-.91.37-1.31.65-.84.58-1.51,1.42-1.88,2.38-.07.18-.13.37-.24.53-.1.16-.26.31-.46.34-.28.05-.55-.14-.68-.4-.13-.25-.13-.55-.12-.83.06-.95.41-1.89,1.03-2.62.1-.11.2-.22.31-.32.93-.3,1.95-.37,2.98-.17.35.07.69.17,1.02.3Z" />
                        </g>
                        <path class="cls-47"
                            d="M237.31,157.78v-16.17h.88c2.52-.01,4.56,2.02,4.57,4.53l.06,17.17h0c-3.05,0-5.52-2.48-5.52-5.53Z" />
                        <path class="cls-47"
                            d="M232.91,157.78v-16.17h-.81c-2.3-.01-4.17,2.02-4.18,4.53l-.06,17.17h0c2.79,0,5.04-2.48,5.04-5.53Z" />
                        <rect class="cls-42" x="237.31" y="152.87" width="5.53" height="2.96"
                            transform="translate(480.14 308.71) rotate(180)" />
                        <rect class="cls-42" x="227.9" y="152.87" width="5.02" height="2.96"
                            transform="translate(460.81 308.71) rotate(180)" />
                    </g>
                    <g>
                        <polygon class="cls-47"
                            points="217.65 156.87 207.47 78.17 75.11 78.17 84.81 156.87 217.65 156.87" />
                        <g>
                            <g>
                                <path class="cls-7"
                                    d="M103.45,129.29c-1.4-.66-2.52-1.59-3.37-2.8-.85-1.21-1.36-2.61-1.53-4.21l-1.48-14.08c-.02-.15.01-.27.09-.35.08-.08.19-.13.34-.13h5.03c.15,0,.27.04.36.13.09.09.15.2.16.35l1.48,14.08c.09.87.41,1.57.97,2.09s1.25.78,2.07.78,1.43-.27,1.88-.8.62-1.22.53-2.07l-1.48-14.08c-.02-.15.01-.27.09-.35.08-.08.19-.13.34-.13h5.04c.15,0,.27.04.36.13.09.09.15.2.16.35l1.48,14.08c.17,1.59-.05,3-.64,4.21-.6,1.21-1.52,2.15-2.76,2.8-1.25.66-2.73.99-4.45.99s-3.28-.33-4.68-.99Z" />
                                <path class="cls-7"
                                    d="M129.45,107.85c.08-.08.19-.13.34-.13h5.04c.15,0,.27.04.36.13.09.09.15.2.16.35l2.24,21.35c.02.15-.01.27-.09.35-.08.08-.19.13-.34.13h-4.81c-.25,0-.46-.11-.61-.32l-7.64-10.83c-.05-.06-.09-.09-.14-.08-.04.01-.06.06-.05.14l1.18,10.61c.02.15-.01.27-.09.35s-.19.13-.34.13h-5.04c-.15,0-.27-.04-.36-.13-.09-.08-.15-.2-.16-.35l-2.24-21.35c-.02-.15.01-.27.09-.35.08-.08.19-.13.34-.13h4.81c.25,0,.46.11.61.32l7.61,10.83c.05.06.09.09.14.08.04,0,.06-.06.05-.14l-1.15-10.61c-.02-.15.01-.27.09-.35Z" />
                                <path class="cls-7"
                                    d="M140.24,129.9c-.09-.08-.15-.2-.17-.35l-2.24-21.35c-.02-.15.01-.27.09-.35.08-.08.19-.13.34-.13h8.19c1.72,0,3.28.3,4.67.89,1.39.6,2.5,1.43,3.34,2.52.84,1.08,1.33,2.34,1.48,3.76l.84,7.97c.15,1.42-.08,2.68-.69,3.76-.61,1.08-1.55,1.92-2.81,2.52-1.27.6-2.76.89-4.48.89h-8.19c-.15,0-.27-.04-.36-.13ZM145.74,124.9h2.74c.72,0,1.29-.28,1.7-.84.41-.56.58-1.3.5-2.21l-.62-5.93c-.1-.91-.41-1.65-.93-2.21-.53-.56-1.17-.85-1.94-.85h-2.71c-.11,0-.15.05-.14.16l1.23,11.73c.01.11.07.16.18.16Z" />
                                <path class="cls-7"
                                    d="M174.03,112.72c-.08.09-.19.13-.34.13h-9.53c-.11,0-.15.05-.14.16l.32,2.99c.01.11.07.16.18.16h5.9c.15,0,.27.04.36.13.09.08.15.2.16.35l.44,4.14c.02.15-.02.27-.09.35-.08.09-.19.13-.34.13h-5.9c-.11,0-.15.05-.14.16l.35,3.31c.01.11.07.16.18.16h9.53c.15,0,.27.04.36.13.09.08.15.2.17.35l.44,4.17c.02.15-.02.27-.09.35-.08.08-.19.13-.34.13h-15.2c-.15,0-.27-.04-.36-.13s-.15-.2-.16-.35l-2.24-21.35c-.02-.15.01-.27.09-.35.08-.08.19-.13.34-.13h15.2c.15,0,.27.04.36.13.09.09.15.2.16.35l.44,4.17c.02.15-.01.27-.09.35Z" />
                                <path class="cls-7"
                                    d="M189.13,129.67l-4.36-8.09c-.05-.08-.12-.13-.2-.13h-1.34c-.11,0-.15.05-.14.16l.83,7.93c.02.15-.01.27-.09.35-.08.08-.19.13-.34.13h-5.03c-.15,0-.27-.04-.36-.13s-.15-.2-.16-.35l-2.24-21.35c-.02-.15.01-.27.09-.35.08-.08.19-.13.34-.13h9.66c1.44,0,2.75.3,3.9.89,1.16.6,2.09,1.43,2.81,2.5.72,1.07,1.15,2.31,1.3,3.71.15,1.4-.07,2.63-.65,3.68s-1.45,1.82-2.61,2.31c-.1.04-.13.12-.07.22l4.93,8.41c.06.13.08.2.09.22.01.11-.02.19-.1.26-.08.06-.18.09-.31.09h-5.35c-.28,0-.47-.12-.58-.35ZM182.18,113.01l.39,3.73c.01.11.07.16.18.16h3c.68,0,1.21-.19,1.6-.56.39-.37.55-.86.49-1.45-.07-.62-.33-1.11-.79-1.48-.46-.37-1.04-.56-1.72-.56h-3c-.11,0-.15.05-.14.16Z" />
                            </g>
                            <g>
                                <path class="cls-7"
                                    d="M101.97,145.51c-.61-.3-1.11-.72-1.49-1.26s-.6-1.17-.68-1.89l-.3-2.82c-.07-.71.02-1.33.29-1.87.26-.54.67-.96,1.22-1.26.55-.3,1.21-.45,1.97-.45s1.45.14,2.06.42c.61.28,1.1.67,1.48,1.18s.6,1.1.67,1.77c0,.08-.06.13-.19.15l-2.19.14h-.04c-.11,0-.17-.05-.18-.15-.04-.38-.18-.69-.43-.91-.25-.23-.56-.34-.93-.34s-.66.11-.86.34c-.2.23-.28.53-.24.91l.32,3c.04.37.18.67.43.9.25.23.56.34.93.34s.66-.11.86-.34c.2-.23.28-.53.24-.9,0-.06,0-.1.04-.12s.08-.04.15-.03l2.22.11c.14,0,.21.06.22.14.07.67-.03,1.26-.29,1.77s-.68.91-1.23,1.19c-.55.28-1.21.42-1.97.42s-1.45-.15-2.06-.45Z" />
                                <path class="cls-7"
                                    d="M110.5,145.55c-.62-.31-1.13-.75-1.51-1.31-.38-.56-.62-1.22-.69-1.96l-.28-2.62c-.08-.73.02-1.37.29-1.92.27-.56.68-.99,1.24-1.3.56-.31,1.22-.46,2-.46s1.48.15,2.11.46c.62.31,1.13.74,1.51,1.3.38.56.61,1.2.69,1.92l.28,2.62c.08.74-.02,1.4-.28,1.96-.27.56-.68,1-1.24,1.31-.56.31-1.23.47-2.01.47s-1.47-.16-2.09-.47ZM113.26,143.38c.21-.26.29-.6.25-1.03l-.29-2.74c-.04-.43-.2-.77-.46-1.03-.26-.26-.59-.38-.98-.38s-.68.13-.89.38c-.21.26-.29.6-.25,1.03l.29,2.74c.04.43.2.77.46,1.03s.59.38.97.38.69-.13.9-.38Z" />
                                <path class="cls-7"
                                    d="M121.96,136.14s.08-.06.15-.06h2.2c.07,0,.12.02.16.06s.07.09.07.15l.98,9.35c0,.07,0,.12-.04.15-.03.04-.08.06-.15.06h-2.11c-.11,0-.2-.05-.27-.14l-3.35-4.74s-.04-.04-.06-.04c-.02,0-.03.03-.02.06l.52,4.65c0,.07,0,.12-.04.15s-.08.06-.15.06h-2.2c-.07,0-.12-.02-.16-.06s-.07-.09-.07-.15l-.98-9.35c0-.07,0-.12.04-.15.03-.04.08-.06.15-.06h2.11c.11,0,.2.05.27.14l3.33,4.75s.04.04.06.03c.02,0,.02-.03.02-.06l-.5-4.65c0-.07,0-.12.04-.15Z" />
                                <path class="cls-7"
                                    d="M128.26,145.57c-.61-.26-1.1-.62-1.48-1.1s-.59-1.02-.65-1.65l-.02-.2c0-.07,0-.12.04-.15.03-.04.08-.06.15-.06h2.16c.07,0,.12.02.16.06.04.04.06.09.07.15v.08c.04.26.2.49.49.69s.67.3,1.13.3c.34,0,.58-.07.72-.22.13-.14.19-.3.17-.48-.02-.21-.13-.36-.33-.46-.2-.1-.55-.23-1.06-.38l-.42-.11c-.95-.28-1.76-.65-2.43-1.12-.67-.47-1.05-1.14-1.15-2.02-.06-.59.04-1.1.31-1.56.27-.45.67-.8,1.2-1.04.53-.24,1.14-.36,1.82-.36.73,0,1.4.13,2.02.4.62.27,1.12.65,1.52,1.14s.62,1.06.69,1.69v.13c.02.06,0,.12-.03.15-.03.04-.08.06-.15.06h-2.16c-.07,0-.12-.02-.16-.06-.04-.04-.07-.09-.07-.15h0c-.03-.29-.18-.55-.46-.78-.28-.23-.63-.34-1.05-.34-.3,0-.53.06-.69.18-.16.12-.23.29-.21.5.02.17.09.31.23.43.14.12.34.23.61.33.27.11.69.25,1.24.41.08.03.42.13,1.02.3s1.12.48,1.56.94c.44.45.69,1,.76,1.64.07.62-.04,1.16-.3,1.61-.27.45-.67.8-1.22,1.04-.54.24-1.19.36-1.94.36s-1.48-.13-2.09-.38Z" />
                                <path class="cls-7"
                                    d="M141.47,136.14s.06.09.07.15l.19,1.84c0,.06,0,.12-.04.15s-.08.06-.15.06h-2.34s-.07.02-.06.07l.76,7.23c0,.07,0,.12-.04.15-.03.04-.08.06-.15.06h-2.21c-.06,0-.12-.02-.16-.06s-.06-.09-.07-.15l-.76-7.23s-.03-.07-.08-.07h-2.27c-.07,0-.12-.02-.16-.06-.04-.04-.06-.09-.07-.15l-.19-1.84c0-.07,0-.12.04-.15s.08-.06.15-.06h7.38c.06,0,.12.02.16.06Z" />
                                <path class="cls-7"
                                    d="M148.22,145.69l-1.91-3.54s-.05-.06-.09-.06h-.59s-.07.02-.06.07l.37,3.47c0,.07,0,.12-.04.15s-.08.06-.15.06h-2.21c-.06,0-.12-.02-.16-.06-.04-.04-.07-.09-.07-.15l-.98-9.35c0-.07,0-.12.04-.15s.08-.06.15-.06h4.23c.63,0,1.2.13,1.71.39.51.26.92.62,1.23,1.09s.5,1.01.57,1.63c.06.61-.03,1.15-.28,1.61-.25.46-.63.8-1.14,1.01-.04.02-.05.05-.03.1l2.16,3.68c.02.06.04.09.04.1,0,.05,0,.08-.04.11s-.08.04-.14.04h-2.34c-.12,0-.21-.05-.25-.15ZM145.18,138.4l.17,1.63s.03.07.08.07h1.31c.3,0,.53-.08.7-.24.17-.16.24-.37.21-.63-.03-.27-.14-.49-.35-.65-.2-.16-.45-.25-.75-.25h-1.31s-.07.02-.06.07Z" />
                                <path class="cls-7"
                                    d="M153.65,145.53c-.61-.29-1.1-.7-1.48-1.23s-.59-1.14-.67-1.84l-.65-6.17c0-.07,0-.12.04-.15s.08-.06.15-.06h2.21c.06,0,.12.02.16.06s.06.09.07.15l.65,6.17c.04.38.18.69.42.91.24.23.55.34.91.34s.63-.12.82-.35c.19-.23.27-.54.23-.91l-.65-6.17c0-.07,0-.12.04-.15s.08-.06.15-.06h2.2c.07,0,.12.02.16.06s.07.09.07.15l.65,6.17c.07.7-.02,1.31-.28,1.84s-.66.94-1.21,1.23c-.55.29-1.2.43-1.95.43s-1.44-.14-2.05-.43Z" />
                                <path class="cls-7"
                                    d="M162.21,145.51c-.61-.3-1.11-.72-1.49-1.26s-.6-1.17-.68-1.89l-.3-2.82c-.07-.71.02-1.33.29-1.87.26-.54.67-.96,1.22-1.26.55-.3,1.21-.45,1.97-.45s1.45.14,2.06.42c.61.28,1.1.67,1.48,1.18s.6,1.1.67,1.77c0,.08-.06.13-.19.15l-2.19.14h-.04c-.11,0-.17-.05-.18-.15-.04-.38-.18-.69-.43-.91-.25-.23-.56-.34-.93-.34s-.66.11-.86.34c-.2.23-.28.53-.24.91l.32,3c.04.37.18.67.43.9.25.23.56.34.93.34s.66-.11.86-.34c.2-.23.28-.53.24-.9,0-.06,0-.1.04-.12s.08-.04.15-.03l2.22.11c.14,0,.21.06.22.14.07.67-.03,1.26-.29,1.77s-.68.91-1.23,1.19c-.55.28-1.21.42-1.97.42s-1.45-.15-2.06-.45Z" />
                                <path class="cls-7"
                                    d="M175.35,136.14s.06.09.07.15l.19,1.84c0,.06,0,.12-.04.15s-.08.06-.15.06h-2.34s-.07.02-.06.07l.76,7.23c0,.07,0,.12-.04.15s-.08.06-.15.06h-2.2c-.07,0-.12-.02-.16-.06s-.06-.09-.07-.15l-.76-7.23s-.03-.07-.08-.07h-2.27c-.07,0-.12-.02-.16-.06-.04-.04-.07-.09-.07-.15l-.19-1.84c0-.07,0-.12.04-.15s.08-.06.15-.06h7.38c.06,0,.12.02.16.06Z" />
                                <path class="cls-7"
                                    d="M177.27,145.79s-.07-.09-.07-.15l-.98-9.35c0-.07,0-.12.04-.15s.08-.06.15-.06h2.21c.06,0,.12.02.16.06s.07.09.07.15l.98,9.35c0,.07,0,.12-.04.15s-.08.06-.15.06h-2.2c-.07,0-.12-.02-.16-.06Z" />
                                <path class="cls-7"
                                    d="M182.64,145.55c-.62-.31-1.13-.75-1.51-1.31-.39-.56-.62-1.22-.69-1.96l-.28-2.62c-.08-.73.02-1.37.29-1.92.27-.56.68-.99,1.24-1.3.56-.31,1.22-.46,2-.46s1.48.15,2.11.46c.62.31,1.13.74,1.51,1.3.38.56.62,1.2.69,1.92l.28,2.62c.08.74-.02,1.4-.28,1.96-.27.56-.68,1-1.24,1.31-.56.31-1.23.47-2.01.47s-1.47-.16-2.09-.47ZM185.4,143.38c.21-.26.29-.6.25-1.03l-.29-2.74c-.04-.43-.2-.77-.46-1.03s-.59-.38-.98-.38-.68.13-.89.38c-.21.26-.29.6-.25,1.03l.29,2.74c.04.43.2.77.46,1.03.26.26.59.38.97.38s.69-.13.9-.38Z" />
                                <path class="cls-7"
                                    d="M194.1,136.14s.08-.06.15-.06h2.21c.06,0,.12.02.16.06s.07.09.07.15l.98,9.35c0,.07,0,.12-.04.15s-.08.06-.15.06h-2.11c-.11,0-.2-.05-.27-.14l-3.34-4.74s-.04-.04-.06-.04-.03.03-.02.06l.52,4.65c0,.07,0,.12-.04.15s-.08.06-.15.06h-2.21c-.06,0-.12-.02-.16-.06-.04-.04-.07-.09-.07-.15l-.98-9.35c0-.07,0-.12.04-.15s.08-.06.15-.06h2.11c.11,0,.2.05.27.14l3.33,4.75s.04.04.06.03c.02,0,.03-.03.02-.06l-.5-4.65c0-.07,0-.12.04-.15Z" />
                            </g>
                        </g>
                        <path class="cls-42"
                            d="M207.47,78.17l10.18,78.7H84.81l-9.71-78.7h132.36M211.86,73.17H69.45l.69,5.61,9.71,78.7.54,4.39h142.95l-.73-5.64-10.18-78.7-.56-4.36h0Z" />
                        <g class="cls-49">
                            <g>
                                <rect class="cls-7" x="83.28" y="119.08" width="213.62" height="4.07"
                                    transform="translate(-21.06 199.97) rotate(-52.98)" />
                                <rect class="cls-7" x="94.95" y="127.88" width="213.62" height="4.07"
                                    transform="translate(-23.44 212.78) rotate(-52.98)" />
                                <rect class="cls-7" x="106.62" y="136.68" width="213.62" height="4.07"
                                    transform="translate(-25.82 225.61) rotate(-52.98)" />
                                <rect class="cls-7" x="118.29" y="145.47" width="213.62" height="4.07"
                                    transform="translate(-28.21 238.42) rotate(-52.98)" />
                                <rect class="cls-7" x="-10.06" y="48.68" width="213.62" height="4.07"
                                    transform="translate(-2 97.41) rotate(-52.97)" />
                                <rect class="cls-7" x="1.61" y="57.48" width="213.62" height="4.07"
                                    transform="translate(-4.38 110.23) rotate(-52.97)" />
                                <rect class="cls-7" x="13.28" y="66.28" width="213.62" height="4.07"
                                    transform="translate(-6.76 123.07) rotate(-52.98)" />
                                <rect class="cls-7" x="24.94" y="75.08" width="213.62" height="4.07"
                                    transform="translate(-9.14 135.88) rotate(-52.98)" />
                                <rect class="cls-7" x="36.61" y="83.88" width="213.62" height="4.07"
                                    transform="translate(-11.52 148.7) rotate(-52.98)" />
                                <rect class="cls-7" x="48.28" y="92.68" width="213.62" height="4.07"
                                    transform="translate(-13.91 161.52) rotate(-52.98)" />
                                <rect class="cls-7" x="59.95" y="101.48" width="213.62" height="4.07"
                                    transform="translate(-16.29 174.33) rotate(-52.98)" />
                                <rect class="cls-7" x="71.61" y="110.28" width="213.62" height="4.07"
                                    transform="translate(-18.67 187.15) rotate(-52.98)" />
                            </g>
                        </g>
                        <g>
                            <path class="cls-7"
                                d="M156.99,102.82h-18.39c-.22,0-.43-.11-.55-.29-.12-.18-.15-.41-.07-.62l7.26-18.39c.09-.23.31-.4.56-.42.25-.02.49.1.62.32l11.13,18.39c.12.21.13.46.01.67-.12.21-.34.34-.58.34ZM139.58,101.5h16.23l-9.82-16.23-6.41,16.23Z" />
                            <path class="cls-7"
                                d="M146.52,96.38s-.07-.08-.07-.14l-.77-5.32c-.01-.13.04-.19.16-.19h1.98c.12,0,.18.06.2.19l.33,5.32c0,.06,0,.1-.04.14-.03.03-.08.05-.14.05h-1.49c-.06,0-.11-.02-.15-.05ZM146.76,99.23c-.25-.22-.38-.51-.42-.85-.04-.35.04-.64.24-.86.2-.22.47-.33.81-.33s.64.11.88.33c.24.22.38.5.42.86.04.34-.05.62-.24.84-.2.23-.47.34-.81.34s-.64-.11-.88-.33Z" />
                        </g>
                    </g>
                    <g>
                        <g>
                            <path class="cls-30"
                                d="M72.68,108.01c-3.64.37-6.57-.28-8.73-1.13-4.91-1.95-7.62-5.63-8.53-8.43-.21-.65.14-1.34.79-1.55.65-.21,1.34.14,1.55.79.7,2.16,2.98,5.26,7.09,6.89,3.47,1.38,9.26,2.1,17.19-2.24.6-.33,1.35-.11,1.67.49.33.6.11,1.35-.49,1.67-3.99,2.19-7.52,3.2-10.55,3.51Z" />
                            <path class="cls-43"
                                d="M64.67,103.28c-.84,1.29-1.73,2.56-2.65,3.8-2.99-2.11-5.5-4.64-7.48-7.43-.61-1.08-.28-2.28.64-2.82h0c.92-.54,2.09-.37,2.84.37,1.8,2.31,4.03,4.38,6.65,6.08Z" />
                        </g>
                        <g>
                            <path class="cls-19"
                                d="M222.78,154.98c3.64.34,6.57-.32,8.72-1.19,4.89-1.98,7.59-5.68,8.47-8.48.21-.65-.15-1.34-.8-1.55-.65-.21-1.34.15-1.55.8-.69,2.16-2.94,5.28-7.05,6.94-3.46,1.4-9.25,2.16-17.2-2.13-.6-.32-1.35-.1-1.67.5-.32.6-.1,1.35.5,1.67,4,2.16,7.54,3.15,10.58,3.44Z" />
                            <path class="cls-43"
                                d="M230.76,150.21c.85,1.29,1.74,2.55,2.68,3.79,2.97-2.13,5.47-4.68,7.43-7.48.61-1.08.27-2.28-.66-2.82h0c-.92-.53-2.09-.35-2.84.39-1.78,2.32-4,4.41-6.61,6.12Z" />
                        </g>
                    </g>
                    <g>
                        <g>
                            <rect class="cls-7" x="121.42" y="234.6" width="3.03" height="5.83"
                                transform="translate(245.87 475.03) rotate(180)" />
                            <path class="cls-26"
                                d="M114.35,243.77c.02-.74.34-2.9,2.08-2.9l2.9-.04.61-1.86c.12-.37.41-.62.73-.62h.47c.12-.01.16.05.16.16.07.97.17,1.44.75,1.43l2.39-.03c.97,0,.91.51.92,1.15,0,0,.03,2.18.02,2.7h-11.04Z" />
                        </g>
                        <g>
                            <rect class="cls-7" x="135.31" y="236.27" width="3.03" height="5.83"
                                transform="translate(273.66 478.37) rotate(180)" />
                            <g>
                                <path class="cls-32"
                                    d="M134.28,242.93h3.54v-2.07c0-.47-.38-.86-.86-.86h-1.83c-.47,0-.86.38-.86.86v2.07Z" />
                                <path class="cls-32"
                                    d="M133.46,245.21h5.65v-2.31c0-.44-.36-.8-.8-.8h-3.19c-.92,0-1.67.75-1.67,1.67v1.44Z" />
                            </g>
                        </g>
                        <g>
                            <path class="cls-32"
                                d="M137.17,203.18l-2.09-3.04c3.39-2.32,10.31-8.02,10.38-8.07l2.34,2.85c-.29.24-7.12,5.85-10.64,8.27Z" />
                            <path class="cls-7"
                                d="M143.1,178.45h16.52c.31,0,.55.25.55.55v23.32c0,3.39-2.75,6.14-6.14,6.14h-5.45c-3.33,0-6.04-2.71-6.04-6.04v-23.42c0-.31.25-.55.55-.55Z"
                                transform="translate(302.71 386.92) rotate(180)" />
                            <path class="cls-32"
                                d="M157.08,215.54h-12.29c-1.24,0-2.25-1.01-2.25-2.25v-5.23h17.63v4.39c0,1.71-1.38,3.09-3.09,3.09Z" />
                            <rect class="cls-17" x="142.54" y="207.47" width="17.64" height="2.43" />
                            <rect class="cls-7" x="146.68" y="207.24" width="4.6" height="2.93" rx=".7" ry=".7" />
                            <rect class="cls-9" x="148.63" y="172.68" width="4.29" height="7.3" rx="2" ry="2"
                                transform="translate(301.55 352.67) rotate(180)" />
                            <g>
                                <polygon class="cls-47"
                                    points="148.65 182.07 150.3 182.07 150.67 196.9 148.11 196.9 148.65 182.07" />
                                <polygon class="cls-47"
                                    points="151.13 179.59 150.31 182.12 148.64 182.13 147.8 179.61 151.13 179.59" />
                            </g>
                            <path class="cls-32"
                                d="M147.4,197.75l7.05-19.33c7,0,6.5,3.39,6.5,6.59v23.46h-10.96l-2.59-10.71Z" />
                            <path class="cls-32"
                                d="M147.59,178.5c-5.79,0-5.04,3.57-5.04,6.51l-.04,23.46h5.75s-.44-29.96-.67-29.96Z" />
                            <g>
                                <path class="cls-9"
                                    d="M144.36,167.08l-.57.05c-.85.07-1.47.81-1.4,1.66l.06.73c.07.85.81,1.47,1.66,1.4l.57-.05-.32-3.79Z" />
                                <path class="cls-32" d="M154.87,161.82h.4v9.51h-2.99v-6.91c0-1.43,1.16-2.6,2.6-2.6Z"
                                    transform="translate(320.98 319.65) rotate(175.18)" />
                                <path class="cls-30"
                                    d="M148.06,159.4c3-.25,5.62,1.99,5.88,4.99.09,1.04.9,7.96-.87,10.22-1.07,1.37-3.08,1.89-4.72,2.08-1.83.21-3.95.1-4.52-1.97-.42-1.54-.78-9.93-.99-12.5-.25-3,2.23-2.56,5.23-2.81Z" />
                                <g>
                                    <path class="cls-32"
                                        d="M145.82,168.16c.03.3-.2.56-.5.59-.3.03-.56-.2-.59-.5-.03-.3.2-.56.5-.59.3-.03.56.2.59.5Z" />
                                    <path class="cls-32"
                                        d="M150.09,167.8c.03.3-.2.56-.5.59-.3.03-.56-.2-.59-.5-.03-.3.2-.56.5-.59.3-.03.56.2.59.5Z" />
                                </g>
                                <g>
                                    <path class="cls-32"
                                        d="M148.27,165.44l2.03-.17c.14-.01.24-.13.23-.27s-.13-.24-.27-.23l-2.03.17c-.14.01-.24.13-.23.27s.13.24.27.23Z" />
                                    <path class="cls-32"
                                        d="M144.13,165.78l2.03-.17c.14-.01.24-.13.23-.27s-.13-.24-.27-.23l-2.03.17c-.14.01-.24.13-.23.27.01.14.13.24.27.23Z" />
                                </g>
                                <path class="cls-29"
                                    d="M150.65,170.06c-.18,1.03-1.35,2.14-2.85,2.26s-2.83-.77-3.19-1.75l6.04-.51Z" />
                                <path class="cls-9"
                                    d="M147.13,169.91c-.16,0-.33-.04-.48-.16-.37-.28.05-1.46.25-1.95.03-.07.11-.1.18-.08.07.03.1.11.08.17-.3.76-.46,1.54-.34,1.64.31.23.75,0,.75-.01.06-.04.15-.01.18.05.04.06.01.15-.05.18-.02,0-.27.15-.56.15Z" />
                                <path class="cls-30"
                                    d="M153.89,166.27l.57-.05c.85-.07,1.59.56,1.66,1.4l.06.73c.07.84-.56,1.59-1.4,1.66l-.57.05-.32-3.79Z" />
                                <path class="cls-32"
                                    d="M150.32,157.41c2.31.36,4.3,1.45,4.51,3.89l.1,1.2-.9.08c-.06.03-.13.06-.2.08l.33,3.89c.04.43-.24.86-.67.94-.49.09-.94-.26-.99-.75l-.19-2.26c-.08-.96-.93-1.67-1.89-1.59,0,0-5.61.47-5.61.47-2.38.2-5.05-1.99-3.01-4.29.73-.82,1.81-1.22,2.89-1.42,1.86-.35,3.75-.54,5.63-.25Z" />
                            </g>
                            <g>
                                <polygon class="cls-31"
                                    points="150.73 182.93 154.93 178.4 152.94 176.48 149.49 179.61 150.73 182.93" />
                                <polygon class="cls-31"
                                    points="148.02 182.93 149.49 179.64 148.66 177 147.62 178.39 148.02 182.93" />
                            </g>
                            <path class="cls-17"
                                d="M154.49,177.99h0c.78.49,1.29,1.32,1.36,2.23l.08.93-1.07.62.75,1.48-7.57,12.73,6.44-17.99Z" />
                            <path class="cls-17"
                                d="M146.34,181.9l-.68-.92.3-.86c.3-.85.91-1.47,1.66-1.69h0l.5,18.49-2.76-13.85.98-1.17Z" />
                            <g>
                                <path class="cls-16"
                                    d="M146.56,201.48c-2.33,0-4.67-.31-6.63-.57l-1.09-.14c-.65-.08-1.11-.67-1.03-1.32.08-.65.68-1.11,1.32-1.02l1.1.14c2.59.34,5.52.73,8.26.46.66-.07,1.23.41,1.29,1.06.06.65-.41,1.23-1.06,1.29-.72.07-1.45.1-2.17.1Z" />
                                <path class="cls-7"
                                    d="M143.87,201.3c-.69,0-1.38-.02-2.07-.06l.18-2.83c1.01.06,2.03.08,3.03.04l.11,2.84c-.41.02-.83.02-1.24.02Z" />
                                <path class="cls-32"
                                    d="M147.52,202.08c-1.17,0-2.26-.08-3.18-.14-.59-.04-1.11-.08-1.53-.08v-3.7c.49,0,1.1.04,1.79.09,2.98.21,7.48.53,10.15-2.31,2.3-2.45,3.03-6.97,2.16-13.44l3.66-.49c1.04,7.73.01,13.12-3.13,16.46-2.84,3.02-6.71,3.61-9.93,3.61Z" />
                            </g>
                            <g>
                                <polygon class="cls-24"
                                    points="117.25 203.04 141.11 203.04 138.04 185.88 114.18 185.88 117.25 203.04" />
                                <polygon class="cls-24"
                                    points="130.08 201.23 153.95 201.23 153.95 201.23 153.46 203.05 130.08 203.04 130.08 201.23" />
                                <polygon class="cls-18"
                                    points="115.85 203.04 139.71 203.04 136.64 185.88 112.78 185.88 115.85 203.04" />
                                <path class="cls-24"
                                    d="M123.68,194.36c.23,1.12,1.3,2.02,2.38,2.02s1.8-.91,1.6-2.02c-.2-1.12-1.27-2.02-2.38-2.02s-1.84.91-1.6,2.02Z" />
                            </g>
                            <path class="cls-16"
                                d="M127.51,204.22c-.56,0-1.05-.39-1.16-.96l-.28-1.49c-.12-.64.3-1.26.94-1.38.64-.13,1.26.3,1.38.94l.28,1.49c.12.64-.3,1.26-.94,1.38-.07.01-.15.02-.22.02Z" />
                        </g>
                        <g>
                            <path class="cls-26"
                                d="M125.28,237.25c0-.12.13-12.54,0-20.18-.02-1.11.46-2.15,1.33-2.81.51-.39,1.12-.58,1.74-.58h0c5.89.01,9.6.06,14.19.2l2.84-5.4c.31.13-9.8.13-17.02.11h-.02c-2.1,0-4.45,1.02-5.92,2.64-1.47,1.63-1.87,3.66-1.83,5.94.13,7.57,0,19.9,0,20.03l4.69.06Z" />
                            <path class="cls-32"
                                d="M156.59,210.3c-3.81-.13-9.03-.3-15.04-.32-1.92,0-3.75.84-5.14,2.37-1.5,1.66-2.35,3.99-2.31,6.4.12,7.56,0,19.91,0,20.03v.31s5.09.06,5.09.06v-.31c0-.12.12-12.47,0-20.18-.02-1.04.18-2.25.95-2.67,1.24-.69,15.51-.44,15.51-.44l2.29-5.2-1.36-.05Z" />
                        </g>
                    </g>
                </g>
                <g id="DESIGNED_BY_FREEPIK" data-name="DESIGNED BY FREEPIK">
                    <g class="cls-21">
                        <g class="cls-45">
                            <line class="cls-12" x1="-.04" y1="-132.39" x2="-210.73" y2="78.3" />
                            <line class="cls-12" x1="6.91" y1="-132.39" x2="-203.78" y2="78.3" />
                            <line class="cls-12" x1="13.86" y1="-132.39" x2="-196.83" y2="78.3" />
                            <line class="cls-12" x1="20.82" y1="-132.39" x2="-189.87" y2="78.3" />
                            <line class="cls-12" x1="27.77" y1="-132.39" x2="-182.92" y2="78.3" />
                            <line class="cls-12" x1="34.72" y1="-132.39" x2="-175.97" y2="78.3" />
                            <line class="cls-12" x1="41.68" y1="-132.39" x2="-169.01" y2="78.3" />
                            <line class="cls-12" x1="48.63" y1="-132.39" x2="-162.06" y2="78.3" />
                            <line class="cls-12" x1="55.59" y1="-132.39" x2="-155.11" y2="78.3" />
                        </g>
                    </g>
                    <g>
                        <g>
                            <path class="cls-4"
                                d="M400.74,56.48c-.08,1.11.09,1.01,0,2.11-.06.73-.62,1.25-1.34,1.3-.52.04-2.17.06-2.6-.09-1.35-.48-.78-2.24-.93-3.32-.24-.59-1.06-.53-1.19.1-.07,1.32.1,2.74,0,4.05-.14,1.84-2.47,1.75-2.71.34-.09-.54,0-1.4-.02-1.99-.02-1.33.02-2.7,0-4.04-.02-.77-1.07-.87-1.24-.12v6.01c-.21,1.59-2.59,1.58-2.73-.04-.12-1.34.09-2.85,0-4.21-.23-.77-1.17-.66-1.24.16-.06.74.18,1.86-.19,2.49-.48.83-1.49.64-2.31.65-.35,1.33-2.42,1.27-2.65-.08-.31-1.79,2.09-2.33,2.65-.68.67-.08,1.68.3,1.78-.66.08-.81-.07-1.75,0-2.56.13-1.56,2.59-1.7,2.73.2.1,1.33-.09,2.78,0,4.12.23.64,1.15.54,1.19-.15.09-1.86-.12-3.84,0-5.69.04-.63.26-1.14.9-1.34.84-.27,1.76.18,1.83,1.1.15,1.92-.12,4.02,0,5.96.06.53.75.76,1.1.34.05-.06.12-.24.13-.33v-4.24c.2-1.2,1.76-1.6,2.48-.6.48.68.14,2.08.25,2.91.05.34.33.52.66.55.47.04,1.71.06,2.15-.02.28-.05.47-.25.52-.53.08-1.17-.09-.67,0-1.83l.77.12ZM383.53,59.5c0-.34-.27-.61-.61-.61s-.61.27-.61.61.27.61.61.61.61-.27.61-.61Z" />
                            <circle class="cls-20" cx="395.28" cy="52.87" r="1.36" />
                            <circle class="cls-13" cx="387.37" cy="52.87" r="1.36" />
                            <path class="cls-22"
                                d="M392.68,51.11c0,.75-.61,1.36-1.36,1.36s-1.36-.61-1.36-1.36.61-1.36,1.36-1.36,1.36.61,1.36,1.36Z" />
                        </g>
                        <g class="cls-8">
                            <path class="cls-40"
                                d="M402.94,46.82c-9.37-9.37-21.18-12.75-26.37-7.56s-1.81,17,7.56,26.37c9.37,9.37,21.18,12.75,26.37,7.56,5.2-5.2,1.81-17-7.56-26.37ZM409.95,72.65c-4.56,4.56-15.66.86-24.79-8.27-9.13-9.13-12.83-20.22-8.27-24.79,4.56-4.56,15.66-.86,24.79,8.27,9.13,9.13,12.83,20.22,8.27,24.79Z" />
                        </g>
                        <path class="cls-36"
                            d="M376.83,67.67c0,1.43,1.16,2.25,2.6,2.25h9.7v-.24c0-.58.47-1.06,1.06-1.06h5.4c.58,0,1.06.47,1.06,1.06v.24h9.7c1.43,0,2.6-.82,2.6-2.25h-32.11Z" />
                        <path class="cls-28"
                            d="M406.67,60h-.73v3.1c0,.66-.53,1.19-1.19,1.19h-23.71c-.66,0-1.19-.53-1.19-1.19v-13.74c0-.66.53-1.19,1.19-1.19h14.99c-.07-.25-.07-.52.02-.79l.28-.85h-15.41c-1.49,0-2.7,1.21-2.7,2.7v14c0,1.49,1.21,2.7,2.7,2.7h23.97c1.49,0,2.7-1.21,2.7-2.7v-3.58c-.24.22-.56.35-.91.35Z" />
                        <path class="cls-37"
                            d="M414.53,51.19l-.95-.31c-.28-.09-.46-.36-.45-.65,0-.13.01-.27.01-.41s0-.27-.01-.41c-.02-.29.17-.56.45-.65l.95-.31c.34-.11.53-.48.42-.81l-.44-1.35c-.11-.34-.48-.53-.81-.42l-.95.31c-.28.09-.58-.01-.74-.26-.15-.23-.31-.45-.48-.66-.18-.23-.19-.55-.02-.78l.59-.81c.21-.29.15-.69-.14-.9l-1.15-.84c-.29-.21-.69-.15-.9.14l-.58.8c-.17.24-.48.33-.75.22-.25-.1-.51-.18-.78-.25-.28-.07-.48-.33-.48-.62v-1c0-.36-.29-.65-.65-.65h-1.42c-.36,0-.65.29-.65.65v1c0,.29-.19.55-.48.62-.27.07-.53.15-.78.25-.27.11-.58.01-.75-.22l-.58-.8c-.21-.29-.61-.35-.9-.14l-1.15.84c-.29.21-.35.61-.14.9l.59.81c.17.24.17.56-.02.78-.17.21-.33.43-.48.66-.16.24-.46.35-.74.26l-.95-.31c-.34-.11-.7.08-.81.42l-.44,1.35c-.11.34.08.7.42.81l.95.31c.28.09.46.36.45.65,0,.13-.01.27-.01.41s0,.27.01.41c.02.29-.17.56-.45.65l-.95.31c-.34.11-.53.48-.42.81l.44,1.35c.11.34.48.53.81.42l.95-.31c.28-.09.58.01.74.26.15.23.31.45.48.66.18.23.19.55.02.78l-.59.81c-.21.29-.15.69.14.9l1.15.84c.29.21.69.15.9-.14l.58-.8c.17-.24.48-.33.75-.22.25.1.51.18.78.25.28.07.48.33.48.62v1c0,.36.29.65.65.65h1.42c.36,0,.65-.29.65-.65v-1c0-.29.19-.55.48-.62.27-.07.53-.15.78-.25.27-.11.58-.01.75.22l.58.8c.21.29.61.35.9.14l1.15-.84c.29-.21.35-.61.14-.9l-.59-.81c-.17-.24-.17-.56.02-.78.17-.21.33-.43.48-.66.16-.24.46-.35.74-.26l.95.31c.34.11.7-.08.81-.42l.44-1.35c.11-.34-.08-.7-.42-.81ZM405.94,54.68c-2.68,0-4.85-2.17-4.85-4.85s2.17-4.85,4.85-4.85,4.85,2.17,4.85,4.85-2.17,4.85-4.85,4.85Z" />
                        <circle class="cls-5" cx="405.94" cy="49.82" r="3.11" />
                        <circle class="cls-11" cx="405.94" cy="49.82" r="6.06" />
                    </g>
                    <g>
                        <g>
                            <g>
                                <path class="cls-15"
                                    d="M367.93,85.64v-4.21h1.73c.37,0,.69.06.96.18s.47.29.62.51c.14.22.22.49.22.81s-.07.57-.22.8c-.14.22-.35.4-.62.52-.27.12-.59.18-.96.18h-1.29l.34-.36v1.58h-.78ZM368.71,84.15l-.34-.38h1.26c.34,0,.6-.07.78-.22.17-.15.26-.35.26-.62s-.09-.47-.26-.62c-.17-.15-.43-.22-.78-.22h-1.26l.34-.38v2.44Z" />
                                <path class="cls-15"
                                    d="M374.39,85.7c-.32,0-.62-.05-.9-.16-.27-.11-.51-.26-.71-.45s-.36-.42-.47-.69c-.11-.26-.17-.55-.17-.87s.06-.61.17-.87c.11-.26.27-.49.48-.69.2-.2.44-.35.72-.45.27-.11.57-.16.9-.16.34,0,.66.06.94.18.28.12.53.29.72.53l-.51.48c-.16-.16-.33-.29-.52-.37-.19-.08-.39-.12-.61-.12s-.42.04-.6.11c-.18.07-.34.17-.48.31-.13.13-.24.29-.32.47-.08.18-.11.38-.11.6s.04.41.11.6c.08.18.18.34.32.47.13.13.29.23.48.31.18.07.38.11.6.11s.42-.04.61-.12c.19-.08.36-.21.52-.38l.51.48c-.2.23-.44.41-.72.53-.28.12-.6.18-.95.18Z" />
                                <path class="cls-15"
                                    d="M376.97,85.64v-4.21h.64l1.84,3.07h-.34l1.81-3.07h.64v4.21s-.74,0-.74,0v-3.05h.15l-1.53,2.57h-.35l-1.56-2.57h.18v3.05h-.75Z" />
                                <path class="cls-15"
                                    d="M382.26,85.64l1.89-4.21h.77l1.9,4.21h-.82l-1.62-3.8h.31l-1.63,3.8h-.8ZM383.14,84.67l.22-.61h2.27l.21.61h-2.69Z" />
                                <path class="cls-15"
                                    d="M387.51,85.64v-4.21h1.73c.37,0,.69.06.96.18s.47.29.62.51c.14.22.22.49.22.81s-.07.57-.22.79c-.14.22-.35.39-.62.51s-.59.18-.96.18h-1.29l.34-.35v1.58h-.78ZM388.3,84.15l-.34-.38h1.26c.34,0,.6-.07.78-.22.17-.15.26-.35.26-.62s-.09-.47-.26-.62c-.17-.15-.43-.22-.78-.22h-1.26l.34-.38v2.44ZM390.27,85.64l-1.06-1.53h.84l1.07,1.53h-.84Z" />
                                <path class="cls-15"
                                    d="M392.06,85.64v-4.21h.78v4.21h-.78ZM392.75,84.65l-.04-.93,2.2-2.29h.88l-1.83,1.95-.43.48-.78.79ZM395,85.64l-1.62-1.92.52-.57,2.01,2.49h-.92Z" />
                                <path class="cls-15"
                                    d="M397.38,84.98h2.38v.66h-3.16v-4.21h3.07v.66h-2.29v2.89ZM397.32,83.18h2.09v.64h-2.09v-.64Z" />
                                <path class="cls-15" d="M401.58,85.64v-3.42h-1.35v-.79h3.67v.79h-1.35v3.42h-.97Z" />
                            </g>
                            <g>
                                <path class="cls-38"
                                    d="M367.93,85.64v-4.21h1.73c.37,0,.69.06.96.18s.47.29.62.51c.14.22.22.49.22.81s-.07.57-.22.8c-.14.22-.35.4-.62.52-.27.12-.59.18-.96.18h-1.29l.34-.36v1.58h-.78ZM368.71,84.15l-.34-.38h1.26c.34,0,.6-.07.78-.22.17-.15.26-.35.26-.62s-.09-.47-.26-.62c-.17-.15-.43-.22-.78-.22h-1.26l.34-.38v2.44Z" />
                                <path class="cls-6"
                                    d="M374.39,85.7c-.32,0-.62-.05-.9-.16-.27-.11-.51-.26-.71-.45s-.36-.42-.47-.69c-.11-.26-.17-.55-.17-.87s.06-.61.17-.87c.11-.26.27-.49.48-.69.2-.2.44-.35.72-.45.27-.11.57-.16.9-.16.34,0,.66.06.94.18.28.12.53.29.72.53l-.51.48c-.16-.16-.33-.29-.52-.37-.19-.08-.39-.12-.61-.12s-.42.04-.6.11c-.18.07-.34.17-.48.31-.13.13-.24.29-.32.47-.08.18-.11.38-.11.6s.04.41.11.6c.08.18.18.34.32.47.13.13.29.23.48.31.18.07.38.11.6.11s.42-.04.61-.12c.19-.08.36-.21.52-.38l.51.48c-.2.23-.44.41-.72.53-.28.12-.6.18-.95.18Z" />
                                <path class="cls-2"
                                    d="M376.97,85.64v-4.21h.64l1.84,3.07h-.34l1.81-3.07h.64v4.21s-.74,0-.74,0v-3.05h.15l-1.53,2.57h-.35l-1.56-2.57h.18v3.05h-.75Z" />
                                <path class="cls-33"
                                    d="M382.26,85.64l1.89-4.21h.77l1.9,4.21h-.82l-1.62-3.8h.31l-1.63,3.8h-.8ZM383.14,84.67l.22-.61h2.27l.21.61h-2.69Z" />
                                <path class="cls-1"
                                    d="M387.51,85.64v-4.21h1.73c.37,0,.69.06.96.18s.47.29.62.51c.14.22.22.49.22.81s-.07.57-.22.79c-.14.22-.35.39-.62.51s-.59.18-.96.18h-1.29l.34-.35v1.58h-.78ZM388.3,84.15l-.34-.38h1.26c.34,0,.6-.07.78-.22.17-.15.26-.35.26-.62s-.09-.47-.26-.62c-.17-.15-.43-.22-.78-.22h-1.26l.34-.38v2.44ZM390.27,85.64l-1.06-1.53h.84l1.07,1.53h-.84Z" />
                                <path class="cls-46"
                                    d="M392.06,85.64v-4.21h.78v4.21h-.78ZM392.75,84.65l-.04-.93,2.2-2.29h.88l-1.83,1.95-.43.48-.78.79ZM395,85.64l-1.62-1.92.52-.57,2.01,2.49h-.92Z" />
                                <path class="cls-35"
                                    d="M397.38,84.98h2.38v.66h-3.16v-4.21h3.07v.66h-2.29v2.89ZM397.32,83.18h2.09v.64h-2.09v-.64Z" />
                                <path class="cls-51" d="M401.58,85.64v-3.42h-1.35v-.79h3.67v.79h-1.35v3.42h-.97Z" />
                            </g>
                        </g>
                        <g>
                            <g>
                                <path class="cls-7" d="M404.56,85.64v-3.42h-1.35v-.79h3.67v.79h-1.35v3.42h-.97Z" />
                                <path class="cls-7"
                                    d="M408.52,84.86h2.29v.78h-3.26v-4.21h3.18v.78h-2.21v2.65ZM408.45,83.12h2.03v.76h-2.03v-.76Z" />
                                <path class="cls-7"
                                    d="M411.18,85.64l1.87-4.21h.96l1.89,4.21h-1.02l-1.55-3.72h.39l-1.55,3.72h-1ZM412.13,84.74l.25-.74h2.18l.25.74h-2.68Z" />
                                <path class="cls-7"
                                    d="M416.49,85.64v-4.21h.81l1.79,2.97h-.43l1.76-2.97h.81v4.21h-.91v-2.81s.17,0,.17,0l-1.42,2.36h-.43l-1.44-2.36h.2v2.81h-.91Z" />
                            </g>
                            <g>
                                <path class="cls-39" d="M404.56,85.64v-3.42h-1.35v-.79h3.67v.79h-1.35v3.42h-.97Z" />
                                <path class="cls-34"
                                    d="M408.52,84.86h2.29v.78h-3.26v-4.21h3.18v.78h-2.21v2.65ZM408.45,83.12h2.03v.76h-2.03v-.76Z" />
                                <path class="cls-41"
                                    d="M411.18,85.64l1.87-4.21h.96l1.89,4.21h-1.02l-1.55-3.72h.39l-1.55,3.72h-1ZM412.13,84.74l.25-.74h2.18l.25.74h-2.68Z" />
                                <path class="cls-23"
                                    d="M416.49,85.64v-4.21h.81l1.79,2.97h-.43l1.76-2.97h.81v4.21h-.91v-2.81s.17,0,.17,0l-1.42,2.36h-.43l-1.44-2.36h.2v2.81h-.91Z" />
                            </g>
                        </g>
                    </g>
                </g>
            </svg>
            <!-- FIN DE LA IMAGEN SVG -->
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section" id="contacto">
        <div class="contact-container">
            <h2 class="contact-title">Contáctanos</h2>

            <div class="contact-grid">
                <div class="contact-item">
                    <i class="fa-solid fa-location-dot"></i>
                    <h3>Ubicación</h3>
                    <p>Bogotá, Colombia<br>Servicio a nivel nacional</p>
                </div>

                <div class="contact-item">
                    <i class="fa-brands fa-whatsapp"></i>
                    <h3>WhatsApp</h3>
                    <p>+57 322 2024365<br>Atención personalizada con FrankQV</p>
                </div>

                <div class="contact-item">
                    <i class="fa-regular fa-clock"></i>
                    <h3>Horario de Atención</h3>
                    <p>Lunes a Viernes<br>9:00 AM - 6:00 PM</p>
                </div>
            </div>
        </div>
    </section>



    <!-- Footer -->
    <footer class="main-footer">
        <div class="footer-content">
            <p class="footer-text">
                2025 - <span>DEV FRANK</span> Todos los derechos reservados
            </p>

            <div class="social-links">
                <a href="https://api.whatsapp.com/send/?phone=573222024365&text=%C2%A1Hola!%20%F0%9F%91%8B%20Bienvenido%2Fa%20%F0%9F%98%8E%0A%0ASoy%20FrankQV%2C%20el%20desarrollador%20detr%C3%A1s%20de%20este%20sistema.%20%F0%9F%9A%80%0A%0AAdem%C3%A1s%20de%20construir%20soluciones%20como%20esta%2C%20tambi%C3%A9n%20ofrezco%3A%0A%E2%9C%85%20Desarrollo%20de%20p%C3%A1ginas%20web%0A%E2%9C%85%20Sistemas%20a%20medida%0A%E2%9C%85%20Automatizaciones%20y%20m%C3%A1s%0A%0AJuntos%20podemos%20llevar%20la%20tecnolog%C3%ADa%20a%20otro%20nivel%20%F0%9F%9A%80%F0%9F%98%8E%0A%0A%C2%BFEn%20qu%C3%A9%20puedo%20ayudarte%20hoy%3F%20%F0%9F%92%AC"
                    target="_blank" rel="noopener noreferrer" title="WhatsApp">
                    <i class="fa-brands fa-whatsapp"></i>
                </a>
                <a href="https://www.tiktok.com/@pcmarkett" target="_blank" rel="noopener noreferrer" title="TikTok">
                    <i class="fa-brands fa-tiktok"></i>
                </a>
                <a href="https://www.youtube.com/@PCMARKETT" target="_blank" rel="noopener noreferrer" title="YouTube">
                    <i class="fa-brands fa-youtube"></i>
                </a>
                <a href="https://github.com/frankqv" target="_blank" rel="noopener noreferrer" title="GitHub">
                    <i class="fa-brands fa-github"></i>
                </a>
            </div>
        </div>
    </footer>

    <script>
    // Menu Toggle Functionality
    function toggleMenu() {
        const navMenu = document.getElementById('navMenu');
        const menuToggle = document.getElementById('menuToggle');

        navMenu.classList.toggle('active');

        // Change icon
        const icon = menuToggle.querySelector('i');
        if (navMenu.classList.contains('active')) {
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-times');
        } else {
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
        }
    }

    function closeMenu() {
        const navMenu = document.getElementById('navMenu');
        const menuToggle = document.getElementById('menuToggle');

        navMenu.classList.remove('active');

        // Reset icon
        const icon = menuToggle.querySelector('i');
        icon.classList.remove('fa-times');
        icon.classList.add('fa-bars');
    }

    // Header scroll effect
    window.addEventListener('scroll', function() {
        const header = document.querySelector('.main-header');
        if (window.scrollY > 100) {
            header.style.background = 'rgba(26, 26, 46, 0.98)';
            header.style.boxShadow = '0 2px 20px rgba(0, 0, 0, 0.3)';
        } else {
            header.style.background = 'rgba(26, 26, 46, 0.95)';
            header.style.boxShadow = 'none';
        }
    });

    // Smooth scrolling for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
        const navMenu = document.getElementById('navMenu');
        const menuToggle = document.getElementById('menuToggle');

        if (!navMenu.contains(event.target) && !menuToggle.contains(event.target)) {
            closeMenu();
        }
    });
    </script>
</body>

</html>