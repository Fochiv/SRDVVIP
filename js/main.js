/* ============================================================
   SRDVVIP — Main JavaScript
   ============================================================ */

/* ---- Translations ---- */
const translations = {
  fr: {
    "nav.home": "Accueil",
    "nav.about": "À propos",
    "nav.menu": "Menu",
    "nav.gallery": "Galerie",
    "nav.services": "Services",
    "nav.testimonials": "Témoignages",
    "nav.contact": "Contact",
    "nav.reserve": "Réserver",
    "hero.tag": "🍽️ Excellence Culinaire Camerounaise",
    "hero.title": "Bienvenue au <span class='gold-text'>SRDVVIP</span>",
    "hero.subtitle": "Découvrez l'excellence culinaire camerounaise dans un cadre élégant et raffiné.",
    "hero.btn1": "Voir notre menu",
    "hero.btn2": "Commander via WhatsApp",
    "stats.dishes": "Spécialités",
    "stats.clients": "Clients satisfaits",
    "stats.days": "Jours ouverts",
    "stats.experience": "Expérience unique",
    "about.tag": "Notre Histoire",
    "about.title": "Un Restaurant <span class='gold-text'>d'Exception</span>",
    "about.desc": "Le SRDVVIP est un restaurant haut de gamme situé à Bessengue, face à la Polyclinique de la Gare à Douala. Nous proposons une cuisine savoureuse, un service de qualité et une expérience culinaire unique dans un cadre accueillant.",
    "about.mission.title": "Notre Mission",
    "about.mission.desc": "Offrir une expérience gastronomique inoubliable à chaque visite.",
    "about.values.title": "Nos Valeurs",
    "about.values.desc": "Qualité, passion, hygiène et excellence au service de nos clients.",
    "about.btn": "Réserver une table",
    "menu.tag": "Nos Plats",
    "menu.title": "Nos <span class='gold-text'>Spécialités</span>",
    "menu.sub": "Chaque plat est préparé avec des ingrédients frais et beaucoup de passion.",
    "filter.all": "Tous",
    "filter.dishes": "Plats",
    "filter.drinks": "Boissons",
    "badge.specialty": "Spécialité",
    "badge.popular": "Populaire",
    "menu.dakere.desc": "Notre plat signature : du mil savamment préparé avec une sauce riche et épicée.",
    "menu.rizsen.name": "Riz Sénégalais",
    "menu.rizsen.desc": "Riz parfumé cuisiné à la sénégalaise, savoureux et généreux.",
    "menu.rizb.name": "Riz Blanc Parfumé",
    "menu.rizb.desc": "Riz blanc de qualité, parfumé et accompagné de sauces maison.",
    "menu.spag.name": "Spaghetti Macaronis",
    "menu.spag.desc": "Pâtes savoureuses préparées avec une sauce maison généreuse.",
    "menu.plantain.name": "Pommes Plantain Viande",
    "menu.plantain.desc": "Plantains dorés accompagnés d'une viande tendre et bien assaisonnée.",
    "menu.poulet.name": "Poulet",
    "menu.poulet.desc": "Poulet grillé ou braisé, mariné aux épices africaines et cuit à la perfection.",
    "menu.frites.name": "Frites",
    "menu.frites.desc": "Frites croustillantes et dorées, parfaites en accompagnement.",
    "menu.eru.name": "Eru",
    "menu.eru.desc": "Plat traditionnel camerounais aux légumes et viandes fumées, riche en saveurs.",
    "menu.couscous.name": "Couscous",
    "menu.couscous.desc": "Couscous moelleux accompagné d'une sauce généreuse et de légumes.",
    "menu.shawarma.name": "Shawarma",
    "menu.shawarma.desc": "Shawarma généreux garni de viande tendre, légumes frais et sauces maison.",
    "menu.boissons.name": "Boissons",
    "menu.boissons.desc": "Large choix de boissons fraîches, jus naturels et sodas pour accompagner votre repas.",
    "menu.eau.name": "Eau Minérale",
    "menu.eau.desc": "Eau minérale pure et fraîche pour rester hydraté tout au long du repas.",
    "menu.more": "Et bien d'autres spécialités vous attendent...",
    "menu.cta": "Voir le menu complet sur WhatsApp",
    "gallery.tag": "Nos Images",
    "gallery.title": "Notre <span class='gold-text'>Galerie</span>",
    "services.tag": "Ce que nous offrons",
    "services.title": "Nos <span class='gold-text'>Services</span>",
    "services.onsite.title": "Restauration sur place",
    "services.onsite.desc": "Profitez d'un cadre élégant et d'une ambiance VVIP pour savourer vos repas.",
    "services.takeaway.title": "Commandes à emporter",
    "services.takeaway.desc": "Passez votre commande et récupérez vos plats rapidement, emballés avec soin.",
    "services.delivery.title": "Livraison",
    "services.delivery.desc": "Nous livrons vos plats préférés directement à votre porte.",
    "services.reservation.title": "Réservations de tables",
    "services.reservation.desc": "Réservez votre table à l'avance pour garantir votre place.",
    "services.events.title": "Événements privés",
    "services.events.desc": "Organisez vos événements privés dans notre espace exclusif.",
    "services.corporate.title": "Repas d'entreprise",
    "services.corporate.desc": "Solutions de restauration pour vos réunions et déjeuners d'affaires.",
    "services.birthday.title": "Anniversaires",
    "services.birthday.desc": "Célébrez vos anniversaires dans un cadre festif et élégant.",
    "services.reception.title": "Réceptions",
    "services.reception.desc": "Mariages, baptêmes, cérémonies — nous gérons tout avec élégance.",
    "hours.title": "⏰ Horaires d'ouverture",
    "hours.everyday": "Tous les jours",
    "hours.note": "* Ouvert 7j/7, de mercredi à mardi.",
    "hours.open": "Nous sommes <span class='gold-text'>ouverts aujourd'hui</span> — Venez nous rendre visite !",
    "hours.btn": "Réserver maintenant",
    "why.tag": "Nos Atouts",
    "why.title": "Pourquoi nous <span class='gold-text'>choisir ?</span>",
    "why.fresh.title": "Produits Frais",
    "why.fresh.desc": "Nous sélectionnons uniquement des ingrédients frais et de qualité pour chaque plat.",
    "why.fast.title": "Service Rapide",
    "why.fast.desc": "Notre équipe efficace garantit un service rapide sans compromis sur la qualité.",
    "why.vvip.title": "Cadre VVIP",
    "why.vvip.desc": "Un environnement luxueux et raffiné pour une expérience culinaire d'exception.",
    "why.hygiene.title": "Hygiène Irréprochable",
    "why.hygiene.desc": "Nos normes d'hygiène sont strictes pour garantir votre santé et votre sécurité.",
    "why.staff.title": "Personnel Qualifié",
    "why.staff.desc": "Une équipe formée et passionnée, toujours à votre écoute pour un service impeccable.",
    "why.price.title": "Excellent Rapport Qualité-Prix",
    "why.price.desc": "Des tarifs accessibles pour une qualité de restaurant haut de gamme.",
    "testi.tag": "Ils nous font confiance",
    "testi.title": "Avis de nos <span class='gold-text'>Clients</span>",
    "testi.1.text": "\"Un restaurant exceptionnel ! Le Dakéré est à tomber par terre. Le cadre est élégant, le service impeccable. Je recommande vivement à tous les amateurs de cuisine camerounaise.\"",
    "testi.1.role": "Cliente fidèle, Douala",
    "testi.2.text": "\"J'organise régulièrement des déjeuners d'affaires ici. Le professionnalisme de l'équipe et la qualité des plats sont toujours au rendez-vous. Meilleur restaurant de Bessengue !\"",
    "testi.2.role": "Homme d'affaires, Douala",
    "testi.3.text": "\"J'ai célébré mon anniversaire ici et ce fut une soirée mémorable ! Tout était parfait : la décoration, la nourriture et surtout l'accueil chaleureux. Merci SRDVVIP !\"",
    "testi.3.role": "Étudiante, Douala",
    "testi.4.text": "\"Le meilleur Eru que j'ai jamais mangé à Douala ! Les prix sont très abordables pour la qualité proposée. Le service est rapide et le personnel très sympa.\"",
    "testi.4.role": "Ingénieur, Douala",
    "res.tag": "Réserver votre table",
    "res.title": "Faites votre <span class='gold-text'>Réservation</span>",
    "res.sub": "Remplissez le formulaire ci-dessous et nous vous confirmerons votre réservation.",
    "res.name": "Nom complet *",
    "res.name.err": "Veuillez entrer votre nom.",
    "res.phone": "Téléphone *",
    "res.phone.err": "Veuillez entrer votre numéro de téléphone.",
    "res.persons": "Nombre de personnes *",
    "res.persons.placeholder": "Sélectionner",
    "res.persons.err": "Sélectionnez le nombre de personnes.",
    "res.date": "Date *",
    "res.date.err": "Choisissez une date.",
    "res.time": "Heure *",
    "res.time.err": "Choisissez une heure (10h–23h).",
    "res.msg": "Message (optionnel)",
    "res.submit": "Réserver maintenant",
    "res.success": "Votre réservation a été envoyée ! Nous vous contacterons très bientôt pour confirmation.",
    "contact.tag": "Nous trouver",
    "contact.title": "Nous <span class='gold-text'>Contacter</span>",
    "contact.address": "Adresse",
    "contact.phone": "Téléphone",
    "contact.call": "Appeler",
    "contact.directions": "Itinéraire",
    "footer.desc": "Un restaurant haut de gamme au cœur de Douala, offrant une expérience culinaire camerounaise exceptionnelle dans un cadre VVIP.",
    "footer.links": "Liens Rapides",
    "footer.hours": "Horaires",
    "footer.contact": "Contact",
    "footer.open247": "Ouvert 7 jours sur 7",
    "footer.copy": "© 2026 SRDVVIP - Super Restaurant Dakéré VVIP. Tous droits réservés.",
    "hours.day": "Tous les jours",
  },
  en: {
    "nav.home": "Home",
    "nav.about": "About",
    "nav.menu": "Menu",
    "nav.gallery": "Gallery",
    "nav.services": "Services",
    "nav.testimonials": "Testimonials",
    "nav.contact": "Contact",
    "nav.reserve": "Book",
    "hero.tag": "🍽️ Cameroonian Culinary Excellence",
    "hero.title": "Welcome to <span class='gold-text'>SRDVVIP</span>",
    "hero.subtitle": "Discover the excellence of Cameroonian cuisine in an elegant and refined setting.",
    "hero.btn1": "See our menu",
    "hero.btn2": "Order via WhatsApp",
    "stats.dishes": "Specialties",
    "stats.clients": "Happy clients",
    "stats.days": "Days open",
    "stats.experience": "Unique experience",
    "about.tag": "Our Story",
    "about.title": "An <span class='gold-text'>Exceptional</span> Restaurant",
    "about.desc": "SRDVVIP is a high-end restaurant located in Bessengue, opposite the Polyclinique de la Gare in Douala. We offer delicious cuisine, quality service and a unique dining experience in a welcoming setting.",
    "about.mission.title": "Our Mission",
    "about.mission.desc": "To offer an unforgettable gastronomic experience on every visit.",
    "about.values.title": "Our Values",
    "about.values.desc": "Quality, passion, hygiene and excellence in the service of our clients.",
    "about.btn": "Book a table",
    "menu.tag": "Our Dishes",
    "menu.title": "Our <span class='gold-text'>Specialties</span>",
    "menu.sub": "Every dish is prepared with fresh ingredients and a lot of passion.",
    "filter.all": "All",
    "filter.dishes": "Dishes",
    "filter.drinks": "Drinks",
    "badge.specialty": "Specialty",
    "badge.popular": "Popular",
    "menu.dakere.desc": "Our signature dish: millet expertly prepared with a rich and spicy sauce.",
    "menu.rizsen.name": "Senegalese Rice",
    "menu.rizsen.desc": "Fragrant rice cooked Senegalese style, flavorful and generous.",
    "menu.rizb.name": "Perfumed White Rice",
    "menu.rizb.desc": "Quality white rice, fragrant and served with house sauces.",
    "menu.spag.name": "Spaghetti Macaroni",
    "menu.spag.desc": "Flavorful pasta prepared with a generous house sauce.",
    "menu.plantain.name": "Plantain with Meat",
    "menu.plantain.desc": "Golden plantains with tender, well-seasoned meat.",
    "menu.poulet.name": "Chicken",
    "menu.poulet.desc": "Grilled or braised chicken, marinated with African spices and cooked to perfection.",
    "menu.frites.name": "Fries",
    "menu.frites.desc": "Crispy golden fries, perfect as a side dish.",
    "menu.eru.name": "Eru",
    "menu.eru.desc": "Traditional Cameroonian dish with vegetables and smoked meats, rich in flavor.",
    "menu.couscous.name": "Couscous",
    "menu.couscous.desc": "Soft couscous served with a generous sauce and vegetables.",
    "menu.shawarma.name": "Shawarma",
    "menu.shawarma.desc": "Generous shawarma loaded with tender meat, fresh vegetables and house sauces.",
    "menu.boissons.name": "Drinks",
    "menu.boissons.desc": "Wide selection of fresh drinks, natural juices and sodas to accompany your meal.",
    "menu.eau.name": "Mineral Water",
    "menu.eau.desc": "Pure and fresh mineral water to stay hydrated throughout your meal.",
    "menu.more": "And many more specialties await you...",
    "menu.cta": "See full menu on WhatsApp",
    "gallery.tag": "Our Photos",
    "gallery.title": "Our <span class='gold-text'>Gallery</span>",
    "services.tag": "What we offer",
    "services.title": "Our <span class='gold-text'>Services</span>",
    "services.onsite.title": "Dine-in",
    "services.onsite.desc": "Enjoy an elegant setting and a VVIP atmosphere to savor your meals.",
    "services.takeaway.title": "Takeaway",
    "services.takeaway.desc": "Place your order and pick up your dishes quickly, carefully packaged.",
    "services.delivery.title": "Delivery",
    "services.delivery.desc": "We deliver your favorite dishes directly to your door.",
    "services.reservation.title": "Table reservations",
    "services.reservation.desc": "Book your table in advance to guarantee your spot.",
    "services.events.title": "Private events",
    "services.events.desc": "Host your private events in our exclusive space.",
    "services.corporate.title": "Corporate meals",
    "services.corporate.desc": "Catering solutions for your business meetings and lunches.",
    "services.birthday.title": "Birthdays",
    "services.birthday.desc": "Celebrate your birthdays in a festive and elegant setting.",
    "services.reception.title": "Receptions",
    "services.reception.desc": "Weddings, baptisms, ceremonies — we handle everything with elegance.",
    "hours.title": "⏰ Opening Hours",
    "hours.everyday": "Every day",
    "hours.note": "* Open 7 days a week, Wednesday to Tuesday.",
    "hours.open": "We are <span class='gold-text'>open today</span> — Come visit us!",
    "hours.btn": "Book now",
    "why.tag": "Our Strengths",
    "why.title": "Why <span class='gold-text'>choose us?</span>",
    "why.fresh.title": "Fresh Produce",
    "why.fresh.desc": "We select only fresh, quality ingredients for every dish.",
    "why.fast.title": "Fast Service",
    "why.fast.desc": "Our efficient team guarantees fast service without compromising quality.",
    "why.vvip.title": "VVIP Setting",
    "why.vvip.desc": "A luxurious and refined environment for an exceptional dining experience.",
    "why.hygiene.title": "Impeccable Hygiene",
    "why.hygiene.desc": "Our hygiene standards are strict to ensure your health and safety.",
    "why.staff.title": "Qualified Staff",
    "why.staff.desc": "A trained and passionate team, always attentive for impeccable service.",
    "why.price.title": "Excellent Value for Money",
    "why.price.desc": "Affordable prices for high-end restaurant quality.",
    "testi.tag": "They trust us",
    "testi.title": "Our <span class='gold-text'>Clients</span> Reviews",
    "testi.1.text": "\"An exceptional restaurant! The Dakéré is amazing. The setting is elegant, the service impeccable. I highly recommend it to all lovers of Cameroonian cuisine.\"",
    "testi.1.role": "Loyal customer, Douala",
    "testi.2.text": "\"I regularly organize business lunches here. The team's professionalism and quality of food are always on point. Best restaurant in Bessengue!\"",
    "testi.2.role": "Businessman, Douala",
    "testi.3.text": "\"I celebrated my birthday here and it was a memorable evening! Everything was perfect: the decor, the food and especially the warm welcome. Thank you SRDVVIP!\"",
    "testi.3.role": "Student, Douala",
    "testi.4.text": "\"The best Eru I've ever had in Douala! The prices are very affordable for the quality offered. The service is fast and the staff very friendly.\"",
    "testi.4.role": "Engineer, Douala",
    "res.tag": "Reserve your table",
    "res.title": "Make your <span class='gold-text'>Reservation</span>",
    "res.sub": "Fill in the form below and we will confirm your reservation.",
    "res.name": "Full name *",
    "res.name.err": "Please enter your name.",
    "res.phone": "Phone *",
    "res.phone.err": "Please enter your phone number.",
    "res.persons": "Number of people *",
    "res.persons.placeholder": "Select",
    "res.persons.err": "Select number of people.",
    "res.date": "Date *",
    "res.date.err": "Please choose a date.",
    "res.time": "Time *",
    "res.time.err": "Choose a time (10 AM–11 PM).",
    "res.msg": "Message (optional)",
    "res.submit": "Book now",
    "res.success": "Your reservation has been sent! We will contact you very soon to confirm.",
    "contact.tag": "Find us",
    "contact.title": "Contact <span class='gold-text'>Us</span>",
    "contact.address": "Address",
    "contact.phone": "Phone",
    "contact.call": "Call",
    "contact.directions": "Directions",
    "footer.desc": "A high-end restaurant in the heart of Douala, offering an exceptional Cameroonian dining experience in a VVIP setting.",
    "footer.links": "Quick Links",
    "footer.hours": "Hours",
    "footer.contact": "Contact",
    "footer.open247": "Open 7 days a week",
    "footer.copy": "© 2026 SRDVVIP - Super Restaurant Dakéré VVIP. All rights reserved.",
    "hours.day": "Every day",
  }
};

/* ---- Current language ---- */
let currentLang = localStorage.getItem('srdvvip-lang') || 'fr';

function applyTranslations(lang) {
  document.querySelectorAll('[data-i18n]').forEach(el => {
    const key = el.getAttribute('data-i18n');
    if (translations[lang] && translations[lang][key] !== undefined) {
      el.innerHTML = translations[lang][key];
    }
  });
  document.documentElement.lang = lang;
}

function setLang(lang) {
  currentLang = lang;
  localStorage.setItem('srdvvip-lang', lang);
  applyTranslations(lang);
  document.querySelectorAll('.lang-btn').forEach(btn => {
    btn.classList.toggle('active', btn.dataset.lang === lang);
  });
}

document.querySelectorAll('.lang-btn').forEach(btn => {
  btn.addEventListener('click', () => setLang(btn.dataset.lang));
});

/* ---- Theme toggle ---- */
const themeToggle = document.getElementById('themeToggle');
let currentTheme = localStorage.getItem('srdvvip-theme') || 'dark';

function applyTheme(theme) {
  document.documentElement.setAttribute('data-theme', theme);
  themeToggle.textContent = theme === 'dark' ? '🌙' : '☀️';
  localStorage.setItem('srdvvip-theme', theme);
  currentTheme = theme;
}

themeToggle.addEventListener('click', () => {
  applyTheme(currentTheme === 'dark' ? 'light' : 'dark');
});

/* ---- Navbar scroll ---- */
const nav = document.getElementById('mainNav');
window.addEventListener('scroll', () => {
  nav.classList.toggle('scrolled', window.scrollY > 60);
}, { passive: true });

/* ---- Active nav link on scroll ---- */
const sections = document.querySelectorAll('section[id]');
window.addEventListener('scroll', () => {
  let current = '';
  sections.forEach(section => {
    if (window.scrollY >= section.offsetTop - 100) {
      current = section.getAttribute('id');
    }
  });
  document.querySelectorAll('.nav-link').forEach(link => {
    link.classList.remove('active');
    if (link.getAttribute('href') === '#' + current) {
      link.classList.add('active');
    }
  });
}, { passive: true });

/* ---- Back to top ---- */
const backToTopBtn = document.getElementById('backToTop');
window.addEventListener('scroll', () => {
  backToTopBtn.classList.toggle('visible', window.scrollY > 400);
}, { passive: true });
backToTopBtn.addEventListener('click', () => {
  window.scrollTo({ top: 0, behavior: 'smooth' });
});

/* ---- Smooth scroll for nav links ---- */
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function(e) {
    const target = document.querySelector(this.getAttribute('href'));
    if (target) {
      e.preventDefault();
      const offset = 80;
      const top = target.getBoundingClientRect().top + window.scrollY - offset;
      window.scrollTo({ top, behavior: 'smooth' });
      // Close mobile nav
      const navCollapse = document.getElementById('navMenu');
      if (navCollapse && navCollapse.classList.contains('show')) {
        navCollapse.classList.remove('show');
      }
    }
  });
});

/* ---- Menu filter ---- */
document.querySelectorAll('.filter-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    this.classList.add('active');
    const filter = this.dataset.filter;
    document.querySelectorAll('.menu-item').forEach(item => {
      if (filter === 'all' || item.dataset.category === filter) {
        item.classList.remove('hidden');
        item.style.display = '';
      } else {
        item.classList.add('hidden');
        item.style.display = 'none';
      }
    });
  });
});

/* ---- Gallery Lightbox ---- */
function openLightbox(el) {
  const img = el.querySelector('img');
  document.getElementById('lightboxImg').src = img.src;
  document.getElementById('lightboxImg').alt = img.alt;
  document.getElementById('lightbox').classList.add('active');
  document.body.style.overflow = 'hidden';
}

function closeLightbox() {
  document.getElementById('lightbox').classList.remove('active');
  document.body.style.overflow = '';
}

document.addEventListener('keydown', e => {
  if (e.key === 'Escape') closeLightbox();
});

/* ---- Reservation form ---- */
document.getElementById('reservationForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const name = document.getElementById('resName');
  const phone = document.getElementById('resPhone');
  const persons = document.getElementById('resPersons');
  const date = document.getElementById('resDate');
  const time = document.getElementById('resTime');
  const message = document.getElementById('resMessage');
  let valid = true;

  [name, phone, persons, date, time].forEach(field => {
    if (!field.value.trim()) {
      field.classList.add('is-invalid');
      valid = false;
    } else {
      field.classList.remove('is-invalid');
      field.classList.add('is-valid');
    }
  });

  if (!valid) return;

  // Send via WhatsApp
  const msgText = `🍽️ *Nouvelle Réservation SRDVVIP*\n\n` +
    `👤 Nom: ${name.value}\n` +
    `📞 Téléphone: ${phone.value}\n` +
    `👥 Personnes: ${persons.value}\n` +
    `📅 Date: ${date.value}\n` +
    `⏰ Heure: ${time.value}\n` +
    `💬 Message: ${message.value || 'Aucun'}`;

  const waUrl = `https://wa.me/237650763338?text=${encodeURIComponent(msgText)}`;
  window.open(waUrl, '_blank');

  // Show success
  this.reset();
  [name, phone, persons, date, time].forEach(f => f.classList.remove('is-valid'));
  document.getElementById('formSuccess').classList.remove('d-none');
  setTimeout(() => document.getElementById('formSuccess').classList.add('d-none'), 6000);
});

/* ---- Set min date for reservation ---- */
const today = new Date().toISOString().split('T')[0];
const resDateInput = document.getElementById('resDate');
if (resDateInput) resDateInput.setAttribute('min', today);

/* ---- AOS init ---- */
AOS.init({
  duration: 800,
  easing: 'ease-out-cubic',
  once: true,
  offset: 60
});

/* ---- Init on load ---- */
document.addEventListener('DOMContentLoaded', () => {
  applyTheme(currentTheme);
  setLang(currentLang);
});

/* Fallback if DOMContentLoaded already fired */
if (document.readyState !== 'loading') {
  applyTheme(currentTheme);
  setLang(currentLang);
}
