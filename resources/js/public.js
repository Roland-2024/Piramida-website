import { initHome } from './template-home';
import { initEducation } from './template-education';
import './template-attractions';
initHome();
initEducation();

document.querySelectorAll('[data-gallery]').forEach(gallery => {
    const images = [...gallery.querySelectorAll('[data-gallery-image]')];
    if (!images.length) return;
    let index = 0;
    const dots = [];
    const show = value => {
        index = (value + images.length) % images.length;
        images.forEach((image, i) => image.hidden = i !== index);
        dots.forEach((dot, i) => { dot.classList.toggle('is-active', i === index); dot.setAttribute('aria-current', String(i === index)); });
    };
    images.forEach((image, i) => {
        const dot = document.createElement('button');
        dot.type = 'button'; dot.setAttribute('aria-label', image.alt || String(i + 1));
        dot.addEventListener('click', () => show(i));
        gallery.querySelector('[data-gallery-dots]')?.append(dot); dots.push(dot);
    });
    gallery.querySelectorAll('[data-gallery-step]').forEach(button => {
        button.hidden = images.length < 2;
        button.addEventListener('click', () => show(index + Number(button.dataset.galleryStep)));
    });
    let startX = 0;
    gallery.addEventListener('touchstart', event => startX = event.touches[0].clientX, { passive: true });
    gallery.addEventListener('touchend', event => { const dx = event.changedTouches[0].clientX - startX; if(Math.abs(dx) > 40) show(index + (dx < 0 ? 1 : -1)); }, { passive: true });
    show(0);
});
// Native dialogs provide keyboard focus trapping and Escape-to-close behavior.
document.querySelectorAll('[data-dialog-open]').forEach(button => {
    button.addEventListener('click', event => { event.preventDefault(); document.getElementById(button.dataset.dialogOpen)?.showModal(); });
});
document.querySelectorAll('[data-dialog-close]').forEach(button => {
    button.addEventListener('click', () => document.getElementById(button.dataset.dialogClose)?.close());
});
document.querySelectorAll('#mobileMenu a[href]').forEach(link => {
    link.addEventListener('click', () => document.getElementById('mobileMenu').close());
});
document.querySelectorAll('[data-carousel]').forEach(carousel => {
    const track = carousel.querySelector('.carousel-track');
    carousel.querySelectorAll('[data-scroll]').forEach(button => {
        button.addEventListener('click', () => track.scrollBy({
            left: Number(button.dataset.scroll) * track.clientWidth,
            behavior: matchMedia('(prefers-reduced-motion: reduce)').matches ? 'instant' : 'smooth',
        }));
    });
});

const intro = document.querySelector('[data-intro]');
if (intro && !matchMedia('(prefers-reduced-motion: reduce)').matches) {
    intro.hidden = false;
    const swipe = document.querySelector('[data-intro-swipe]');
    const stripes = intro.querySelector('.intro-stripes');
    const words = [...intro.querySelectorAll('.intro-word')];
    ['#2f6fef', '#3f8ff5', '#4aaaf2', '#3fc9d8', '#3fd9a8', '#5fe37f', '#8fef55', '#b6f21f', '#caf987'].forEach(color => {
        const stripe = document.createElement('span');
        stripe.style.setProperty('--stripe-color', color);
        stripes.append(stripe);
    });
    words.forEach((word, index) => word.style.setProperty('--word-color', ['#ef17c9', '#f7ef0a', '#2fe4e0'][index]));
    const start = performance.now() + 500;
    const frame = now => {
        if (intro.hidden) return;
        const t = Math.max(0, Math.min(1, (now - start) / 3000));
        const outer = (t < .5 ? 4 * t ** 3 : 1 - (-2 * t + 2) ** 3 / 2) * 100;
        stripes.style.setProperty('--reveal-inner', `${Math.max(0, outer - 30)}%`);
        stripes.style.setProperty('--reveal-outer', `${outer}%`);
        words.forEach((word, index) => word.classList.toggle('lit', outer >= index * 35));
        if (t < 1) requestAnimationFrame(frame);
    };
    requestAnimationFrame(frame);
    const timers = [
        setTimeout(() => { swipe.hidden = false; }, 4000),
        setTimeout(() => { intro.hidden = true; }, 4754),
        setTimeout(() => { swipe.hidden = true; }, 5300),
    ];
    const skip = () => {
        timers.forEach(clearTimeout);
        intro.hidden = true;
        swipe.hidden = true;
    };
    intro.querySelector('button').addEventListener('click', () => {
        skip();
        document.querySelector('#main-content').focus({ preventScroll: true });
    });
    // Never leave the visual intro covering someone navigating by keyboard.
    document.addEventListener('keydown', event => {
        if (event.key === 'Tab' || event.key === 'Escape') {
            skip();
        }
    });
}


// Enhance real request sections into native dialogs, retaining a no-JS form.
document.querySelectorAll('[data-request-panel]').forEach(panel => {
    const dialog = document.createElement('dialog');
    dialog.className = 'template-dialog';
    dialog.setAttribute('aria-label', panel.querySelector('.registration-title')?.textContent || 'Request');
    panel.before(dialog);
    dialog.append(panel);
    document.querySelectorAll('[data-request-open]').forEach(link => {
        if (link.dataset.requestOpen === panel.id) link.addEventListener('click', event => { event.preventDefault(); dialog.showModal(); });
    });
    panel.querySelectorAll('[data-request-close]').forEach(button => button.addEventListener('click', () => dialog.close()));
    if (panel.dataset.feedback === 'true') dialog.showModal();
});

const video = document.querySelector('#piramidaVideo');
const playBadge = document.querySelector('#playBadge');
if (video && playBadge) {
    playBadge.addEventListener('click', event => {
        event.preventDefault();
        video.controls = true;
        video.play().catch(() => { video.controls = true; });
    });
    video.addEventListener('play', () => playBadge.classList.add('is-playing'));
    video.addEventListener('pause', () => playBadge.classList.remove('is-playing'));
}

const eventSection = document.querySelector('#eventsSection');
if (eventSection) {
    const slides = [...eventSection.querySelectorAll('.events-slide')];
    const info = eventSection.querySelector('#eventsInfo');
    let active = 0;
    const show = index => {
        active = Math.max(0, Math.min(slides.length - 1, index));
        slides.forEach((slide, i) => {
            slide.classList.toggle('is-active', i === active);
            slide.classList.toggle('is-prev', i < active);
            slide.classList.toggle('is-next', i > active);
            slide.inert = i !== active;
        });
        info.classList.remove('is-visible');
        eventSection.querySelector('[data-event-step="-1"]')?.toggleAttribute('disabled', active === 0);
        eventSection.querySelector('[data-event-step="1"]')?.toggleAttribute('disabled', active === slides.length - 1);
    };
    eventSection.querySelectorAll('[data-event-step]').forEach(button => button.addEventListener('click', () => show(active + Number(button.dataset.eventStep))));
    eventSection.addEventListener('keydown', event => {
        if (['ArrowDown', 'ArrowUp'].includes(event.key)) { event.preventDefault(); show(active + (event.key === 'ArrowDown' ? 1 : -1)); }
    });
    let lastWheel = 0;
    eventSection.addEventListener('wheel', event => {
        const next = active + Math.sign(event.deltaY);
        if (!event.deltaY || next < 0 || next >= slides.length) return;
        event.preventDefault();
        if (Date.now() - lastWheel > 900) { show(next); lastWheel = Date.now(); }
    }, { passive: false });
    eventSection.querySelectorAll('.slot-diagonal').forEach(card => {
        const enter = () => {
            card.parentElement.classList.add('has-hover');
            card.classList.add('is-hovered');
            info.querySelector('.events-info-title').textContent = card.querySelector('.slot-title').textContent;
            info.querySelector('.events-info-date').textContent = card.querySelector('.slot-date').textContent;
            info.classList.add('is-visible');
        };
        const leave = () => { card.parentElement.classList.remove('has-hover'); card.classList.remove('is-hovered'); info.classList.remove('is-visible'); };
        card.addEventListener('mouseenter', enter); card.addEventListener('focusin', enter);
        card.addEventListener('mouseleave', leave); card.addEventListener('focusout', leave);
    });
    show(0);
}
document.querySelectorAll('.event-spaces-carousel').forEach(carousel => {
    const track = carousel.querySelector('.event-spaces-card-track');
    carousel.querySelectorAll('.event-spaces-carousel-arrow').forEach(button => button.addEventListener('click', () => {
        track.scrollBy({ left: track.clientWidth * (button.classList.contains('event-spaces-carousel-arrow-left') ? -1 : 1), behavior: 'smooth' });
    }));
});
document.querySelectorAll('.leasing-form-carousel').forEach(carousel => {
    const track = carousel.querySelector('.leasing-form-gallery');
    carousel.querySelectorAll('.leasing-form-carousel-arrow').forEach(button => button.addEventListener('click', () => {
        track.scrollBy({ left: track.clientWidth * (button.classList.contains('leasing-form-carousel-arrow-left') ? -1 : 1), behavior: 'smooth' });
    }));
});

const leasingForm = document.querySelector('[data-leasing-form]');
if (leasingForm) {
    const mobile = matchMedia('(max-width:767px)');
    const hero = leasingForm.closest('.leasing-form-hero');
    const steps = [...leasingForm.querySelectorAll('fieldset')];
    const submit = leasingForm.querySelector('.leasing-submit-button');
    const back = leasingForm.querySelector('.leasing-back-button');
    let step = 0;
    leasingForm.noValidate = true;
    const render = () => {
        leasingForm.classList.toggle('is-mobile-stepper', mobile.matches);
        hero.dataset.currentStep = leasingForm.dataset.currentStep = mobile.matches ? String(step + 1) : '';
        steps.forEach((fieldset, i) => fieldset.dataset.stepActive = String(i === step));
        submit.textContent = mobile.matches && step < steps.length - 1 ? submit.dataset.nextLabel : submit.dataset.submitLabel;
    };
    leasingForm.addEventListener('submit', event => {
        const controls = mobile.matches && step < steps.length - 1 ? steps[step].querySelectorAll('input,select') : leasingForm.querySelectorAll('input,select');
        const invalid = [...controls].find(control => !control.checkValidity());
        if (invalid) {
            event.preventDefault();
            const index = steps.indexOf(invalid.closest('fieldset'));
            if (index >= 0) step = index;
            render(); invalid.reportValidity(); invalid.focus();
        } else if (mobile.matches && step < steps.length - 1) {
            event.preventDefault(); step++; render(); leasingForm.scrollIntoView({block:'start'});
        }
    });
    back.addEventListener('click', () => { step = Math.max(0, step - 1); render(); });
    mobile.addEventListener('change', render);
    render();
}
