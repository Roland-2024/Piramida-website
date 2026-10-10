export function initEventsArchive() {
    const archive = document.querySelector('#eventsArchive');
    const section = archive?.querySelector('#eventsSection');
    if (!section) return;
    const track = section.querySelector('#eventsTrack');
    const mobile = archive.querySelector('#eventsMobile');
    const info = section.querySelector('#eventsInfo');
    const status = archive.querySelector('#eventsLoadStatus');
    const more = archive.querySelector('#eventsLoadMore');
    const slides = [...track.children];
    let nextUrl = more?.href;
    let active = 0;
    let busy = false;
    let lockedUntil = 0;
    let failed = false;
    const isMobile = () => matchMedia('(max-width: 768px)').matches;

    function bindCards(slide) {
        slide.querySelectorAll('.slot-diagonal').forEach(card => {
            const enter = () => {
                slide.classList.add('has-hover');
                card.classList.add('is-hovered');
                info.querySelector('.events-info-title').textContent = card.querySelector('.slot-title').textContent;
                info.querySelector('.events-info-date').textContent = card.querySelector('.slot-date').textContent;
                info.classList.add('is-visible');
            };
            const leave = () => {
                slide.classList.remove('has-hover');
                card.classList.remove('is-hovered');
                info.classList.remove('is-visible');
            };
            card.addEventListener('mouseenter', enter);
            card.addEventListener('focusin', enter);
            card.addEventListener('mouseleave', leave);
            card.addEventListener('focusout', leave);
        });
    }
    function show(index) {
        active = index;
        slides.forEach((slide, i) => {
            slide.classList.toggle('is-active', i === active);
            slide.classList.toggle('is-prev', i < active);
            slide.classList.toggle('is-next', i > active);
            slide.inert = i !== active;
            slide.classList.remove('has-hover');
            slide.querySelectorAll('.is-hovered').forEach(card => card.classList.remove('is-hovered'));
        });
        info.classList.remove('is-visible');
        lockedUntil = Date.now() + (matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 900);
    }
    async function loadNext() {
        if (busy || !nextUrl) return false;
        busy = true;
        status.textContent = archive.dataset.loading;
        more.hidden = true;
        archive.setAttribute('aria-busy', 'true');
        try {
            // Reuse the crawlable HTML route so AJAX and no-JS links share publication rules.
            const response = await fetch(nextUrl, { headers: { Accept: 'text/html' } });
            if (!response.ok) throw new Error('Event request failed');
            const page = new DOMParser().parseFromString(await response.text(), 'text/html');
            if (!page.querySelector('#eventsArchive')) throw new Error('Invalid event response');
            const added = [...page.querySelectorAll('#eventsTrack .events-slide')];
            added.forEach(slide => {
                slide.classList.replace('is-active', 'is-next');
                slide.inert = true;
                track.append(slide);
                slides.push(slide);
                bindCards(slide);
            });
            mobile.append(...page.querySelectorAll('#eventsMobile .reel-item'));
            nextUrl = page.querySelector('#eventsLoadMore')?.href;
            if (nextUrl) more.href = nextUrl;
            failed = false;
            status.textContent = '';
            // Commit the offscreen position before moving a freshly appended group.
            void track.offsetHeight;
            return added.length > 0;
        } catch {
            failed = true;
            status.textContent = archive.dataset.error;
            more.hidden = false;
            return false;
        } finally {
            busy = false;
            archive.removeAttribute('aria-busy');
        }
    }
    async function advance(direction) {
        if (busy || Date.now() < lockedUntil) return;
        const target = active + direction;
        if (target < 0) return;
        if (target >= slides.length && (failed || !(await loadNext()))) return;
        show(target);
    }
    section.addEventListener('wheel', event => {
        if (event.ctrlKey || !event.deltaY || Math.abs(event.deltaX) > Math.abs(event.deltaY)) return;
        const direction = Math.sign(event.deltaY);
        if ((direction < 0 && active === 0) || (direction > 0 && active === slides.length - 1 && !nextUrl)) return;
        event.preventDefault();
        void advance(direction);
    }, { passive: false });
    section.addEventListener('keydown', event => {
        if (event.key !== 'ArrowDown' && event.key !== 'ArrowUp') return;
        event.preventDefault();
        void advance(event.key === 'ArrowDown' ? 1 : -1);
    });
    mobile.addEventListener('scroll', () => {
        if (isMobile() && !failed && mobile.scrollTop + mobile.clientHeight >= mobile.scrollHeight - mobile.clientHeight / 2) {
            void loadNext();
        }
    }, { passive: true });
    more?.addEventListener('click', event => {
        event.preventDefault();
        failed = false;
        if (isMobile()) void loadNext();
        else void advance(1);
    });
    if (more) more.hidden = true;
    slides.forEach(bindCards);
}
