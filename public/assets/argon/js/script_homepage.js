      window.addEventListener('load', function() {
    const preloader = document.getElementById('preloader');
    preloader.style.display = 'none';
  });

    // Hamburger menu
    const hamburgerBtn = document.getElementById('hamburger-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    let menuOpen = false;
    hamburgerBtn.addEventListener('click', () => {
      menuOpen = !menuOpen;
      if(menuOpen) {
        mobileMenu.classList.remove('opacity-0','pointer-events-none');
        mobileMenu.classList.add('opacity-100');
      } else {
        mobileMenu.classList.add('opacity-0','pointer-events-none');
        mobileMenu.classList.remove('opacity-100');
      }
    });
    mobileMenu.addEventListener('click', (e) => {
      if(e.target === mobileMenu) {
        menuOpen = false;
        mobileMenu.classList.add('opacity-0','pointer-events-none');
        mobileMenu.classList.remove('opacity-100');
      }
    });
    // Navbar transparan saat discroll
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
      if(window.scrollY > 10) {
        navbar.classList.add('bg-white/80','backdrop-blur','shadow-md');
        navbar.classList.remove('bg-white');
      } else {
        navbar.classList.remove('bg-white/80','backdrop-blur');
        navbar.classList.add('bg-white');
      }
    });
    
    // Auto-scroll testimonial carousel
    const track = document.getElementById('testimonial-track');
    const total = track.children.length;
    let idx = 0;
    setInterval(() => {
      idx = (idx + 1) % total;
      track.style.transform = `translateX(-${idx * 100}%)`;
    }, 4000);

    // Data outlet
    const outlets = {
      surabaya: {
        map: 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3957.9681779681105!2d112.775769691843!3d-7.244461222219543!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd7f9b0b298d195%3A0x1b301a8958c157c6!2sKue%20Ijo%20Pandan%20Asli!5e0!3m2!1sid!2sid!4v1753154879994!5m2!1sid!2sid',
        img: 'https://images.unsplash.com/photo-1519125323398-675f0ddb6308?auto=format&fit=crop&w=400&q=80',
        title: 'Pusat Surabaya',
        address: 'Jl. Lebak Jaya II No.26, RT.005/RW.04, Gading, Kec. Tambaksari, Surabaya, Jawa Timur 60134',
        hours: 'Senin - Minggu, 08.00 - 20.00',
        contact: 'Telp: 083849268988',
        email: 'pandanaslisbyadm@gmail.com',
        directions: 'https://maps.app.goo.gl/FBLH5zD3sq1wBYit8'
      },
      malang: {
        map: 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3951.4563986431112!2d112.6579965!3d-7.951697299999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd629afaf867ba3%3A0x9aa041e45fac81a8!2sJl.%20Graha%20Pelita%20Asri%20No.b29%2C%20Pandanwangi%2C%20Kec.%20Blimbing%2C%20Kota%20Malang%2C%20Jawa%20Timur%2065124!5e0!3m2!1sid!2sid!4v1753156426758!5m2!1sid!2sid',
        img: 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=400&q=80',
        title: 'Outlet Malang',
        address: 'Jl. Graha Pelita Asri No.b29, Pandanwangi, Kec. Blimbing, Kota Malang, Jawa Timur 65124',
        hours: 'Senin - Minggu, 08.00 - 21.00',
        contact: 'Telp: 082131338971',
        email:'pandanaslimalangadm@gmail.com',
        directions: 'https://maps.app.goo.gl/fyw33cyHhwBuAyQE8'
      },
      denpasar: {
        map: 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3943.832310607487!2d115.22523580000001!3d-8.7074693!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd2410d9d4cfba5%3A0xf2eb89848c9d9c5a!2sGg.%20Ikan%20Arwana%20No.6%2C%20Sesetan%2C%20Denpasar%20Selatan%2C%20Kota%20Denpasar%2C%20Bali%2080224!5e0!3m2!1sid!2sid!4v1753156639022!5m2!1sid!2sid',
        img: 'https://images.unsplash.com/photo-1464983953574-0892a716854b?auto=format&fit=crop&w=400&q=80',
        title: 'Outlet Denpasar',
        address: 'Gg. Ikan Arwana No.6, Sesetan, Denpasar Selatan, Kota Denpasar, Bali 80224',
        hours: 'Senin - Minggu, 09.00 - 21.00',
        contact: 'Telp: -',
        email:'pandanaslibaliadm@hmail.com',
        directions: 'https://maps.app.goo.gl/YA8hKxqigziBTiXf7'
      }
    };
    // Button event
    document.getElementById('btn-surabaya').onclick = function() {
      setOutlet('surabaya');
      setActive(this);
    };
    document.getElementById('btn-malang').onclick = function() {
      setOutlet('malang');
      setActive(this);
    };
    document.getElementById('btn-denpasar').onclick = function() {
      setOutlet('denpasar');
      setActive(this);
    };
    function setOutlet(key) {
      const o = outlets[key];
      document.getElementById('outlet-map').src = o.map;
      document.getElementById('outlet-img').src = o.img;
      document.getElementById('outlet-title').textContent = o.title;
      document.getElementById('outlet-address').textContent = o.address;
      document.getElementById('outlet-hours').textContent = o.hours;
      document.getElementById('outlet-contact').textContent = o.contact;
      document.getElementById('outlet-email').textContent = 'Email: ' + o.email;
      document.getElementById('outlet-directions').href = o.directions;
    }
    function setActive(btn) {
      document.querySelectorAll('.outlet-btn').forEach(b => {
        b.classList.remove('bg-[#8BA870]','text-white');
        b.classList.add('bg-white','text-[#8BA870]','border','border-[#8BA870]');
      });
      btn.classList.add('bg-[#8BA870]','text-white');
      btn.classList.remove('bg-white','text-[#8BA870]','border','border-[#8BA870]');
    }

    AOS.init({
        duration: 800, // durasi animasi dalam ms
        once: false, // apakah animasi hanya terjadi sekali
    });