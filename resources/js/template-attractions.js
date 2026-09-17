(function () {
    const root = document.getElementById("attractions");
if (!root?.querySelector("[data-cards]")?.children.length) return;
    const cards = root.querySelector("[data-cards]");
    const prevBtn = root.querySelector("[data-prev]");
    const nextBtn = root.querySelector("[data-next]");
    const tagEl = root.querySelector("[data-caption-tag]");
    const titleEl = root.querySelector("[data-caption-title]");
    const descEl = root.querySelector("[data-caption-desc]");
    const dotsWrap = root.querySelector("[data-attraction-dots]");
    const TOTAL = cards.children.length;
    const ROLE_CLASSES = ["is-center", "is-near-left", "is-near-right", "is-far-left", "is-far-right", "is-hidden"];

    function centerCard() {
        const children = Array.from(cards.children);
        return children[Math.floor(children.length / 2)];
    }

    // Assigns a role class by distance from center — works for any number
    // of cards, not just five.
    function applyRoles() {
        const children = Array.from(cards.children);
        const centerIndex = Math.floor(children.length / 2);
        children.forEach((el, i) => {
            el.classList.remove(...ROLE_CLASSES);
            const d = i - centerIndex;
            if (d === 0) el.classList.add("is-center");
            else if (d === -1) el.classList.add("is-near-left");
            else if (d === 1) el.classList.add("is-near-right");
            else if (d === -2) el.classList.add("is-far-left");
            else if (d === 2) el.classList.add("is-far-right");
            else el.classList.add("is-hidden");
        });
    }

    // Reads caption content from the card's own markup (.attraction-content
    // divs) instead of data-* attributes.
    function readContent(card) {
        const content = card.querySelector(".attraction-content");
        if (!content) return { tag: "", title: "", desc: "" };
        return {
            tag: content.querySelector(".tag")?.textContent.trim() || "",
            title: content.querySelector(".title")?.textContent.trim() || "",
            desc: content.querySelector(".desc")?.textContent.trim() || "",
        };
    }

    function syncCaption() {
        const active = centerCard();
        if (!active) return;
        const { tag, title, desc } = readContent(active);
        [tagEl, titleEl, descEl].forEach(el => el.style.opacity = 0);
        setTimeout(() => {
            tagEl.textContent = tag;
            titleEl.textContent = title;
            descEl.textContent = desc;
            [tagEl, titleEl, descEl].forEach(el => el.style.opacity = 1);
        }, 150);
    }

    function render() { applyRoles(); syncCaption(); updateActiveDot(); }
    function next() { cards.appendChild(cards.firstElementChild); render(); }
    function prev() { cards.insertBefore(cards.lastElementChild, cards.firstElementChild); render(); }

    // Dots mirror the arrows on mobile (CSS shows one or the other,
    // never both). Each dot maps to a card's original data-index,
    // which stays put on the element through every reorder.
    function buildDots() {
        if (!dotsWrap) return;
        dotsWrap.innerHTML = "";
        for (let i = 0; i < TOTAL; i++) {
            const dot = document.createElement("button");
            dot.type = "button";
            dot.className = "attraction-dot";
            dot.setAttribute("aria-label", `Go to attraction ${i + 1}`);
            dot.addEventListener("click", () => goToIndex(i));
            dotsWrap.appendChild(dot);
        }
    }

    function updateActiveDot() {
        if (!dotsWrap) return;
        const activeIndex = Number(centerCard().dataset.index);
        Array.from(dotsWrap.children).forEach((dot, i) => {
            dot.classList.toggle("is-active", i === activeIndex);
        });
    }

    function goToIndex(target) {
        const currentIndex = Number(centerCard().dataset.index);
        let diff = target - currentIndex;
        if (diff > TOTAL / 2) diff -= TOTAL;
        if (diff < -TOTAL / 2) diff += TOTAL;
        for (let k = 0; k < Math.abs(diff); k++) diff > 0 ? next() : prev();
    }

    buildDots();
    root.addEventListener('keydown', e => { if(e.key === 'ArrowLeft') prev(); if(e.key === 'ArrowRight') next(); });

    prevBtn.addEventListener("click", prev);
    nextBtn.addEventListener("click", next);

    // Swipe
    let startX = 0, dragging = false, fired = false;
    cards.addEventListener("pointerdown", (e) => {
        if (e.target.closest("[data-prev],[data-next]")) return;
        dragging = true; fired = false; startX = e.clientX;
        window.addEventListener("pointermove", onMove);
        window.addEventListener("pointerup", onUp);
    });
    function onMove(e) {
        if (!dragging || fired) return;
        const dx = e.clientX - startX;
        if (Math.abs(dx) > 60) { fired = true; dx < 0 ? next() : prev(); }
    }
    function onUp() {
        dragging = false;
        window.removeEventListener("pointermove", onMove);
        window.removeEventListener("pointerup", onUp);
    }

    render();
})();

(function () {
    const root = document.getElementById("experiences");
if (!root?.querySelector("[data-exp-track]")?.children.length) return;
    const track = root.querySelector("[data-exp-track]");
    const prevBtn = root.querySelector("[data-exp-prev]");
    const nextBtn = root.querySelector("[data-exp-next]");
    const dotsWrap = root.querySelector("[data-exp-dots]");
    const cards = Array.from(track.children);

    // How many cards fit in one viewport-width "page" — recalculated on
    // resize since card width changes across breakpoints.
    function cardsPerPage() {
        const cardWidth = cards[0].getBoundingClientRect().width;
        const gap = parseFloat(getComputedStyle(track).columnGap || getComputedStyle(track).gap || "0");
        return Math.max(1, Math.round(track.clientWidth / (cardWidth + gap)));
    }

    function pageCount() {
        return Math.max(1, Math.ceil(cards.length / cardsPerPage()));
    }

    function buildDots() {
        dotsWrap.innerHTML = "";
        const pages = pageCount();
        for (let i = 0; i < pages; i++) {
            const dot = document.createElement("button");
            dot.type = "button";
            dot.className = "exp-dot";
            dot.setAttribute("aria-label", `Go to page ${i + 1}`);
            dot.addEventListener("click", () => goToPage(i));
            dotsWrap.appendChild(dot);
        }
        updateActiveDot();
    }

    function goToPage(i) {
        const perPage = cardsPerPage();
        const targetCard = cards[i * perPage];
        if (targetCard) {
            track.scrollTo({ left: targetCard.offsetLeft - track.offsetLeft, behavior: "smooth" });
        }
    }

    function updateActiveDot() {
        const perPage = cardsPerPage();
        const scrolledCards = Math.round(track.scrollLeft / (cards[0].getBoundingClientRect().width + 20));
        const activePage = Math.min(pageCount() - 1, Math.round(scrolledCards / perPage));
        Array.from(dotsWrap.children).forEach((dot, i) => {
            dot.classList.toggle("is-active", i === activePage);
        });
    }

    function scrollByCards(direction) {
        const cardWidth = cards[0].getBoundingClientRect().width;
        const gap = parseFloat(getComputedStyle(track).columnGap || getComputedStyle(track).gap || "0");
        track.scrollBy({ left: direction * (cardWidth + gap), behavior: "smooth" });
    }

    prevBtn.addEventListener("click", () => scrollByCards(-1));
    nextBtn.addEventListener("click", () => scrollByCards(1));

    let scrollTimeout;
    track.addEventListener("scroll", () => {
        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(updateActiveDot, 100);
    });

    let resizeTimeout;
    window.addEventListener("resize", () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(buildDots, 150);
    });

    // Drag/swipe: native touch scrolling already handles finger swipes
    // fine since the track is `overflow-x: auto` — leave that alone.
    // This only adds mouse-drag support on desktop (pointerType
    // "mouse"), so it never fights the browser's own touch handling.
    let dragging = false;
    let dragStartX = 0;
    let dragStartScroll = 0;
    let dragMoved = false;

    track.addEventListener("pointerdown", (e) => {
        if (e.pointerType !== "mouse") return;
        if (e.target.closest("[data-exp-prev],[data-exp-next]")) return;
        dragging = true;
        dragMoved = false;
        dragStartX = e.clientX;
        dragStartScroll = track.scrollLeft;
        track.style.scrollSnapType = "none";
        track.style.scrollBehavior = "auto";
        // Preserve anchor clicks; window pointer handlers finish a drag.
    });

    track.addEventListener("pointermove", (e) => {
        if (!dragging || e.pointerType !== "mouse") return;
        const dx = e.clientX - dragStartX;
        if (Math.abs(dx) > 5) dragMoved = true;
        track.scrollLeft = dragStartScroll - dx;
    });

    function endDrag(e) {
        if (!dragging) return;
        dragging = false;
        track.style.scrollSnapType = "";
        track.style.scrollBehavior = "";
        if (e && e.pointerId !== undefined && track.hasPointerCapture(e.pointerId)) {
            track.releasePointerCapture(e.pointerId);
        }
        // Snap to the nearest card after a free drag.
        const cardWidth = cards[0].getBoundingClientRect().width;
        const gap = parseFloat(getComputedStyle(track).columnGap || getComputedStyle(track).gap || "0");
        const nearestIndex = Math.round(track.scrollLeft / (cardWidth + gap));
        const target = cards[Math.max(0, Math.min(cards.length - 1, nearestIndex))];
        if (target) {
            track.scrollTo({ left: target.offsetLeft - track.offsetLeft, behavior: "smooth" });
        }
    }

    track.addEventListener("pointerup", (e) => {
        if (e.pointerType === "mouse") endDrag(e);
    });
    track.addEventListener("pointercancel", (e) => {
        if (e.pointerType === "mouse") endDrag(e);
    });
    track.addEventListener("pointerleave", (e) => {
        if (dragging && e.pointerType === "mouse") endDrag(e);
    });

    // Prevent an accidental click firing on a card right after a drag.
    track.addEventListener("click", (e) => {
        if (dragMoved) {
            e.preventDefault();
            e.stopPropagation();
        }
    }, true);

    buildDots();
})();
