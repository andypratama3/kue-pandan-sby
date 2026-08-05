// ===== Utilities =====
function normalizePhoneTo62(raw) {
    if (!raw) return "";
    let d = String(raw).replace(/\D+/g, "");
    if (d.startsWith("+62")) d = "62" + d.slice(3);
    else if (d.startsWith("0")) d = "62" + d.slice(1);
    else if (d.startsWith("8")) d = "62" + d;
    return d;
}
function buildWaLink(raw) {
    const n = normalizePhoneTo62(raw);
    return n ? `https://wa.me/${n}` : "#";
}
function buildMailto(email, { subject = "", body = "" } = {}) {
    if (!email) return "#";
    const q = new URLSearchParams();
    if (subject) q.set("subject", subject);
    if (body) q.set("body", body);
    return `mailto:${email}${q.toString() ? "?" + q.toString() : ""}`;
}
function setHrefOrDisable(el, href) {
    if (!el) return;
    const disabled = !href || href === "#";
    el.href = disabled ? "#" : href;
    el.target = disabled ? "_self" : "_blank";
    el.classList.toggle("pointer-events-none", disabled);
    el.classList.toggle("opacity-60", disabled);
}

// ===== On load: preloader =====
window.addEventListener("load", function () {
    const preloader = document.getElementById("preloader");
    if (preloader) preloader.style.display = "none";
});

// ===== AOS & Zoom =====
if (window.AOS) AOS.init({ duration: 700, once: true });
if (window.mediumZoom)
    mediumZoom(".zoomable", {
        background: "rgba(0,0,0,0.7)",
        margin: 24,
        scrollOffset: 40,
    });

// ===== Hamburger =====
const hamburgerBtn = document.getElementById("hamburger-btn");
const mobileMenu = document.getElementById("mobile-menu");
let menuOpen = false;
if (hamburgerBtn && mobileMenu) {
    hamburgerBtn.addEventListener("click", () => {
        menuOpen = !menuOpen;
        mobileMenu.classList.toggle("opacity-0", !menuOpen);
        mobileMenu.classList.toggle("pointer-events-none", !menuOpen);
        mobileMenu.classList.toggle("opacity-100", menuOpen);
    });
    mobileMenu.addEventListener("click", (e) => {
        if (e.target === mobileMenu) {
            menuOpen = false;
            mobileMenu.classList.add("opacity-0", "pointer-events-none");
            mobileMenu.classList.remove("opacity-100");
        }
    });
}

// ===== Navbar scroll =====
const navbar = document.getElementById("navbar");
window.addEventListener("scroll", () => {
    if (!navbar) return;
    if (window.scrollY > 10) {
        navbar.classList.add("bg-white/80", "backdrop-blur", "shadow-md");
        navbar.classList.remove("bg-white");
    } else {
        navbar.classList.remove("bg-white/80", "backdrop-blur");
        navbar.classList.add("bg-white");
    }
});

// ===== Enhanced Testimonial carousel =====
const track = document.getElementById("testimonial-track");
const prevBtn = document.getElementById("testimonial-prev");
const nextBtn = document.getElementById("testimonial-next");
const dots = document.querySelectorAll(".testimonial-dot");

if (track && track.children.length) {
    const total = track.children.length;
    let currentIdx = 0;
    let autoSlideInterval;
    let isTransitioning = false;

    // Update carousel position and indicators
    function updateCarousel(index, smooth = true) {
        if (isTransitioning) return;

        isTransitioning = true;
        currentIdx = index;

        // Update track position
        track.style.transform = `translateX(-${currentIdx * 100}%)`;

        // Update dot indicators
        dots.forEach((dot, i) => {
            if (i === currentIdx) {
                dot.classList.remove("bg-gray-300");
                dot.classList.add("bg-[#8BA870]");
            } else {
                dot.classList.remove("bg-[#8BA870]");
                dot.classList.add("bg-gray-300");
            }
        });

        // Reset transition flag after animation
        setTimeout(() => {
            isTransitioning = false;
        }, 700);
    }

    // Auto-slide functionality
    function startAutoSlide() {
        autoSlideInterval = setInterval(() => {
            const nextIndex = (currentIdx + 1) % total;
            updateCarousel(nextIndex);
        }, 5000);
    }

    function stopAutoSlide() {
        if (autoSlideInterval) {
            clearInterval(autoSlideInterval);
        }
    }

    // Navigation button handlers
    if (prevBtn) {
        prevBtn.addEventListener("click", () => {
            if (isTransitioning) return;
            stopAutoSlide();
            const prevIndex = currentIdx === 0 ? total - 1 : currentIdx - 1;
            updateCarousel(prevIndex);
            startAutoSlide();
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener("click", () => {
            if (isTransitioning) return;
            stopAutoSlide();
            const nextIndex = (currentIdx + 1) % total;
            updateCarousel(nextIndex);
            startAutoSlide();
        });
    }

    // Dot indicator handlers
    dots.forEach((dot, index) => {
        dot.addEventListener("click", () => {
            if (isTransitioning || index === currentIdx) return;
            stopAutoSlide();
            updateCarousel(index);
            startAutoSlide();
        });
    });

    // Pause auto-slide on hover
    const carousel = document.getElementById("testimonial-carousel");
    if (carousel) {
        carousel.addEventListener("mouseenter", stopAutoSlide);
        carousel.addEventListener("mouseleave", startAutoSlide);
    }

    // Touch/swipe support for mobile
    let startX = 0;
    let endX = 0;

    if (carousel) {
        carousel.addEventListener(
            "touchstart",
            (e) => {
                startX = e.touches[0].clientX;
            },
            { passive: true }
        );

        carousel.addEventListener(
            "touchmove",
            (e) => {
                endX = e.touches[0].clientX;
            },
            { passive: true }
        );

        carousel.addEventListener(
            "touchend",
            () => {
                if (!startX || !endX) return;

                const diff = startX - endX;
                const threshold = 50;

                if (Math.abs(diff) > threshold) {
                    stopAutoSlide();
                    if (diff > 0) {
                        // Swipe left - next slide
                        const nextIndex = (currentIdx + 1) % total;
                        updateCarousel(nextIndex);
                    } else {
                        // Swipe right - previous slide
                        const prevIndex =
                            currentIdx === 0 ? total - 1 : currentIdx - 1;
                        updateCarousel(prevIndex);
                    }
                    startAutoSlide();
                }

                startX = 0;
                endX = 0;
            },
            { passive: true }
        );
    }

    // Initialize carousel
    updateCarousel(0);
    startAutoSlide();
}

// ===== Data outlet (tambahan: wa & social) =====
const outlets = {
    surabaya: {
        map: "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3957.9681779681105!2d112.775769691843!3d-7.244461222219543!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd7f9b0b298d195%3A0x1b301a8958c157c6!2sKue%20Ijo%20Pandan%20Asli!5e0!3m2!1sid!2sid!4v1753154879994!5m2!1sid!2sid",
        img:
            window.assetUrls?.outletImages?.surabaya ||
            "/assets/homepage/b1.jpg",
        title: "Pusat Surabaya",
        address:
            "Jl. Lebak Jaya II, RT.005/RW.04, Gading, Kec. Tambaksari, Surabaya, Jawa Timur 60134",
        hours: "Buka Setiap Hari, 06.00 - 23.00",
        contact: "Telp: 082144834303",
        wa: "082144834303",
        email: "pandanaslisbyadm@gmail.com",
        directions: "https://maps.app.goo.gl/FBLH5zD3sq1wBYit8",
        social: {
            instagram: "https://www.instagram.com/pandanasli",
            tiktok: "#",
            facebook: "#",
        },
    },
    malang: {
        map: "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1474.2871613935379!2d112.61826423631473!3d-7.991349604326536!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7882a7ed5dfbbb%3A0x268a0601debbb206!2sJl.%20Pelatuk%20No.16%2C%20Sukun%2C%20Kec.%20Sukun%2C%20Kota%20Malang%2C%20Jawa%20Timur%2065147!5e0!3m2!1sen!2sid!4v1765946881452!5m2!1sen!2sid",
        img:
            window.assetUrls?.outletImages?.malang || "/assets/homepage/b2.jpg",
        title: "Outlet Malang",
        address:
            "Jl. Pelatuk No. 16 Sukun, Kota Malang, Jawa Timur 65147",
        hours: "Buka Setiap Hari, 06.00 - 23.00",
        contact: "Telp: 082131338971",
        wa: "082131338971", // pilih Malang -> direct ke WA Malang
        email: "pandanaslimalangadm@gmail.com",
        directions: "https://maps.app.goo.gl/UhTpwAjYUuMyZfYQA",
        social: {
            instagram: "https://www.instagram.com/pandanaslimalang",
            tiktok: "https://www.tiktok.com/@pandanasli_malang",
            facebook: "https://www.facebook.com/pandanaslimalang",
        },
    },
    denpasar: {
        map: "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3943.832310607487!2d115.22523580000001!3d-8.7074693!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd2410d9d4cfba5%3A0xf2eb89848c9d9c5a!2sGg.%20Ikan%20Arwana%20No.6%2C%20Sesetan%2C%20Denpasar%20Selatan%2C%20Kota%20Denpasar%2C%20Bali%2080224!5e0!3m2!1sid!2sid!4v1753156639022!5m2!1sid!2sid",
        img:
            window.assetUrls?.outletImages?.denpasar ||
            "/assets/homepage/b3.jpg",
        title: "Outlet Denpasar",
        address:
            "Gg. Ikan Arwana, Sesetan, Denpasar Selatan, Kota Denpasar, Bali 80224",
        hours: "Buka Setiap Hari, 06.00 - 23.00",
        contact: "Telp: 082338901223",
        wa: "082338901223", // tidak ada WA? nanti auto nonaktif
        email: "pandanaslibaliadm@gmail.com",
        directions: "https://maps.app.goo.gl/YA8hKxqigziBTiXf7",
        social: {
            instagram: "https://www.instagram.com/pandan_baliasli",
            tiktok: "https://www.tiktok.com/@pandanasli_bali",
            facebook: "#",
        },
    },
};

// ===== Buttons =====
const btnSby = document.getElementById("btn-surabaya");
const btnMlg = document.getElementById("btn-malang");
const btnDps = document.getElementById("btn-denpasar");
if (btnSby)
    btnSby.onclick = function () {
        setOutlet("surabaya");
        setActive(this);
    };
if (btnMlg)
    btnMlg.onclick = function () {
        setOutlet("malang");
        setActive(this);
    };
if (btnDps)
    btnDps.onclick = function () {
        setOutlet("denpasar");
        setActive(this);
    };

// ===== Core: setOutlet =====
function setOutlet(key) {
    const o = outlets[key];
    const outletContent = document.getElementById("outlet-content");
    if (!o || !outletContent) return;

    // Fade out
    outletContent.style.opacity = "0";
    outletContent.style.transform = "translateY(10px)";
    outletContent.style.transition = "all 0.3s ease-in-out";

    setTimeout(() => {
        // Map & gambar
        const mapEl = document.getElementById("outlet-map");
        const imgEl = document.getElementById("outlet-img");
        if (mapEl) mapEl.src = o.map;
        if (imgEl) imgEl.src = o.img;

        // Teks umum
        const titleEl = document.getElementById("outlet-title");
        const addrEl = document.getElementById("outlet-address");
        const hoursEl = document.getElementById("outlet-hours");
        if (titleEl) titleEl.textContent = o.title;
        if (addrEl) addrEl.textContent = o.address;
        if (hoursEl) hoursEl.textContent = o.hours;

        // WhatsApp (anchor dengan id="outlet-contact")
        const contactA = document.getElementById("outlet-contact");
        if (contactA) {
            const telText =
                o.contact && o.contact.trim() ? o.contact : "Telp: -";
            const telNo = o.wa || telText.replace(/\D+/g, "");
            const waHref = buildWaLink(telNo);

            // jika ada span text di dalam <a>, update; kalau tidak, set text langsung
            const span = document.getElementById("outlet-contact-text");
            if (span) span.textContent = telText;
            else contactA.textContent = telText;

            setHrefOrDisable(contactA, waHref);
        }

        // Email (anchor dengan id="outlet-email")
        const emailA = document.getElementById("outlet-email");
        const emailTextSpan = document.getElementById("outlet-email-text");
        const mailHref = buildMailto(o.email, {
            subject: `Pertanyaan Outlet ${o.title}`,
            body: `Halo ${o.title},%0D%0A%0D%0ASaya ingin menanyakan ...`,
        });
        if (emailA) {
            setHrefOrDisable(emailA, o.email ? mailHref : "#");
            if (emailTextSpan)
                emailTextSpan.textContent = o.email || "Email: -";
            else emailA.textContent = o.email || "Email: -";
        }

        // Directions
        const dirA = document.getElementById("outlet-directions");
        if (dirA) setHrefOrDisable(dirA, o.directions);

        // Sosial media (anchor id: social-tiktok/instagram/facebook)
        // setHrefOrDisable(
        //     document.getElementById("social-instagram"),
        //     o.social?.instagram || "#"
        // );
        // setHrefOrDisable(
        //     document.getElementById("social-tiktok"),
        //     o.social?.tiktok || "#"
        // );
        // setHrefOrDisable(
        //     document.getElementById("social-facebook"),
        //     o.social?.facebook || "#"
        // );

        // Helper untuk mengambil username dari URL social media
        function getSocialHandle(url) {
            if (!url || url === "#") return "-";
            try {
                const path = new URL(url).pathname.split("/").filter(Boolean);
                if (url.includes("tiktok.com/@")) {
                    return path.find((p) => p.startsWith("@")) || "-";
                }
                return path[path.length - 1] || "-";
            } catch (e) {
                return "-";
            }
        }

        // Update Social Media Links and Text
        function updateSocial(platform, url) {
            const linkEl = document.getElementById(`social-${platform}`);
            const textEl = document.getElementById(`social-${platform}-text`);
            setHrefOrDisable(linkEl, url);
            if (textEl) {
                textEl.textContent = getSocialHandle(url);
            }
        }

        updateSocial("instagram", o.social?.instagram || "#");
        updateSocial("tiktok", o.social?.tiktok || "#");
        updateSocial("facebook", o.social?.facebook || "#");

        // Fade in
        outletContent.style.opacity = "1";
        outletContent.style.transform = "translateY(0)";
    }, 300);
}

// ===== Active button state =====
function setActive(btn) {
    document.querySelectorAll(".outlet-btn").forEach((b) => {
        b.classList.remove("bg-brand-deep", "text-white");
        b.classList.add(
            "bg-white",
            "text-brand-deep",
            "border",
            "border-brand/30",
            "hover:border-brand"
        );
        b.style.transform = "scale(1)";
        b.style.transition = "all 0.3s ease-in-out";
    });
    btn.classList.add("bg-brand-deep", "text-white");
    btn.classList.remove(
        "bg-white",
        "text-brand-deep",
        "border",
        "border-brand/30",
        "hover:border-brand"
    );
    btn.style.transform = "scale(1.05)";
    btn.style.transition = "all 0.3s ease-in-out";
    setTimeout(() => {
        btn.style.transform = "scale(1)";
    }, 150);
}

// Set default outlet saat halaman siap
document.addEventListener("DOMContentLoaded", () => setOutlet("surabaya"));
