export function initHome() {
if (!document.querySelector('#cardTrack .card-snap')) return;
const track = document.getElementById("cardTrack");
const prevBtn = document.getElementById("prevBtn");
const nextBtn = document.getElementById("nextBtn");

// Let the browser handle vertical page scrolling natively, but hand
// horizontal gestures on this track entirely to our own swipe logic below.
// Without this, touchstart can kick off a native horizontal scroll before
// pointermove's preventDefault() has a chance to run, so the drag and our
// next()/prev() snap end up fighting each other.
track.style.touchAction = "pan-y";

// ---- Infinite loop setup: clone all cards before and after ----
const originalCards = Array.from(track.children);
const cardCount = originalCards.length;

originalCards.forEach((card) => {
  const clone = card.cloneNode(true);
  clone.querySelectorAll("a").forEach(link => link.tabIndex = -1);
  track.appendChild(clone); // after
});
originalCards
  .slice()
  .reverse()
  .forEach((card) => {
    const clone = card.cloneNode(true);
    clone.querySelectorAll("a").forEach(link => link.tabIndex = -1);
    track.insertBefore(clone, track.firstChild); // before
  });

function cardStep() {
  const card = track.querySelector(".card-snap");
  if (!card) return 0;
  // getComputedStyle(track).columnGap can be the string "normal" (not just
  // empty) when no gap is set, and "normal" is truthy — so the old
  // `|| 16` fallback never ran and parseFloat("normal") silently produced
  // NaN, breaking every scroll calculation downstream. Guard on the parsed
  // number instead of the raw string.
  const parsedGap = parseFloat(getComputedStyle(track).columnGap);
  const gap = Number.isNaN(parsedGap) ? 16 : parsedGap;
  return card.getBoundingClientRect().width + gap;
}

function currentIndex() {
  return Math.round(track.scrollLeft / cardStep());
}
function goTo(index, smooth = true) {
  track.style.scrollBehavior = smooth && !matchMedia("(prefers-reduced-motion: reduce)").matches ? "smooth" : "auto";

  track.scrollLeft = index * cardStep();
}

// ---- Detect and mark whichever card is centered in the viewport ----
function updateActiveCard() {
  const cards = track.querySelectorAll(".card-snap");
  const trackRect = track.getBoundingClientRect();
  const centerX = trackRect.left + trackRect.width / 2;

  let closest = null;
  let closestDist = Infinity;

  cards.forEach((card) => {
    const rect = card.getBoundingClientRect();
    const cardCenter = rect.left + rect.width / 2;
    const dist = Math.abs(cardCenter - centerX);
    if (dist < closestDist) {
      closestDist = dist;
      closest = card;
    }
  });

  cards.forEach((card) => card.classList.toggle("is-active", card === closest));
}

// Start centered in the "originals" block (skip the prepended clones)
function init() {
  goTo(cardCount, false);
  updateActiveCard();
}
requestAnimationFrame(init);
window.addEventListener("load", init);

function next() {
  goTo(currentIndex() + 1);
}
function prev() {
  goTo(currentIndex() - 1);
}

prevBtn.addEventListener("click", prev);
nextBtn.addEventListener("click", next);

// Silently re-center once a scroll settles into a clone zone,
// so the loop feels endless without a visible jump.
// Also update the active card live while scrolling (rAF-throttled).
let settleTimer;
let scrollTicking = false;

track.addEventListener("scroll", () => {
  if (!scrollTicking) {
    requestAnimationFrame(() => {
      updateActiveCard();
      scrollTicking = false;
    });
    scrollTicking = true;
  }

  clearTimeout(settleTimer);
  settleTimer = setTimeout(() => {
    const idx = currentIndex();
    if (idx >= cardCount * 2) goTo(idx - cardCount, false);
    else if (idx < cardCount) goTo(idx + cardCount, false);
    updateActiveCard();
  }, 80);
});

window.addEventListener("resize", () => {
  goTo(currentIndex(), false);
  updateActiveCard();
});

// ---- Swipe (pointer-based) — snaps to next/prev, no live drag-follow ----
let startX = 0;
let startY = 0;
let pointerDown = false;
let swiped = false;
const SWIPE_THRESHOLD = 40;

track.addEventListener("pointerdown", (e) => {
  pointerDown = true;
  swiped = false;
  startX = e.clientX;
  startY = e.clientY;
  // Keep normal link clicks intact; native horizontal scrolling handles touch.
});

track.addEventListener("pointermove", (e) => {
  if (!pointerDown) return;
  const dx = e.clientX - startX;
  const dy = e.clientY - startY;
  if (Math.abs(dx) > Math.abs(dy) && Math.abs(dx) > 10) {
    e.preventDefault(); // stop native scroll/selection during a horizontal swipe
  }
});

track.addEventListener("pointerup", (e) => {
  if (!pointerDown) return;
  pointerDown = false;
  const dx = e.clientX - startX;
  if (Math.abs(dx) > SWIPE_THRESHOLD) {
    swiped = true;
    dx < 0 ? next() : prev();
  }
});

track.addEventListener("pointercancel", () => {
  pointerDown = false;
});

// Prevent click-through on links/images right after a swipe
track.addEventListener(
  "click",
  (e) => {
    if (swiped) {
      e.preventDefault();
      e.stopPropagation();
      swiped = false;
    }
  },
  true,
);

track.querySelectorAll("img").forEach((img) => {
  img.addEventListener("dragstart", (e) => e.preventDefault());
});

}
(function () {
    const scrollEl = document.getElementById('newsScroll');
    if (!scrollEl) return;
    const cards = Array.from(document.querySelectorAll('#newsScroll .news-card'));
    const dots = Array.from(document.querySelectorAll('#newsDots .news-dot'));

    function setActiveDot(index) {
        dots.forEach((dot, i) => dot.classList.toggle('active', i === index));
    }

    function updateActiveFromScroll() {
        const scrollCenter = scrollEl.scrollLeft + scrollEl.clientWidth / 2;
        let closestIndex = 0;
        let closestDistance = Infinity;
        cards.forEach((card, i) => {
            const cardCenter = card.offsetLeft + card.clientWidth / 2;
            const distance = Math.abs(cardCenter - scrollCenter);
            if (distance < closestDistance) {
                closestDistance = distance;
                closestIndex = i;
            }
        });
        setActiveDot(closestIndex);
    }

    scrollEl.addEventListener('scroll', () => {
        window.requestAnimationFrame(updateActiveFromScroll);
    }, { passive: true });

    dots.forEach((dot) => {
        dot.addEventListener('click', () => {
            const index = parseInt(dot.dataset.index, 10);
            cards[index].scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
        });
    });

    setActiveDot(0);
})();
