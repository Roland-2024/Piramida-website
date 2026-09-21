export function initEducation() {
const MOBILE_BREAKPOINT = 767;

const DESKTOP_GAP = 14;
const CENTER_W = 280;
const CENTER_H = 310;
const NEXT_H = 360;
const MAX_W = 280;
const MAX_H = 540;

const MOBILE_SIDE_SPACE = 30;
const MOBILE_GAP = 20;
const MOBILE_CARD_H = 512;
const MOBILE_CARD_MAX_W = 380;
const MOBILE_RADIUS = 28;

const SNAP_EASE = matchMedia("(prefers-reduced-motion: reduce)").matches ? 1 : 0.16;

const track = document.getElementById("track");
const viewport = document.getElementById("viewport");
const dotsContainer = document.getElementById("carouselDots");
const activeSlideTitle = document.getElementById("activeSlideTitle");
const activeSlideDescription = document.getElementById(
  "activeSlideDescription",
);

function cleanText(value) {
  return value.trim().replace(/\s+/g, " ");
}

function readSlide(card, index) {
  const image = card.querySelector("img");
  const title = card.querySelector("[data-slide-title]");
  const description = card.querySelector("[data-slide-description]");

  return {
    label: cleanText(title?.textContent || image?.alt || `Slide ${index + 1}`),
    description: cleanText(description?.textContent || ""),
    template: card,
  };
}

if (track && viewport) {
  const items = Array.from(track.querySelectorAll(".card")).map(readSlide);

  if (items.length > 0) {
    const N = items.length;
    const COPIES = 3;
    const TOTAL = COPIES * N;
    const dots = [];
    const allCards = [];
    let layout = {
      isMobile: window.innerWidth <= MOBILE_BREAKPOINT,
      viewportWidth: 0,
      viewportCenter: 0,
      cardWidth: CENTER_W,
      gap: DESKTOP_GAP,
      slotWidth: CENTER_W + DESKTOP_GAP,
    };

    track.replaceChildren();

    if (dotsContainer) {
      items.forEach((item, index) => {
        const dot = document.createElement("button");
        dot.type = "button";
        dot.className =
          "dot h-2 w-2 rounded-full bg-gray-500 transition-all";
        dot.setAttribute("aria-label", `Go to ${item.label}`);
        dot.addEventListener("click", () => {
          activeIndex = N + index;
          snapTarget = calculateOffset(activeIndex);
          requestAnimationTick();
        });
        dotsContainer.appendChild(dot);
        dots.push(dot);
      });
    }

    function refreshLayout() {
      const viewportWidthValue = viewport.offsetWidth;
      const mobile = window.innerWidth <= MOBILE_BREAKPOINT;
      const cardWidth = mobile
        ? Math.min(
            MOBILE_CARD_MAX_W,
            Math.max(240, viewportWidthValue - MOBILE_SIDE_SPACE - 56),
          )
        : CENTER_W;
      const gap = mobile ? MOBILE_GAP : DESKTOP_GAP;

      layout = {
        isMobile: mobile,
        viewportWidth: viewportWidthValue,
        viewportCenter: viewportWidthValue / 2,
        cardWidth,
        gap,
        slotWidth: cardWidth + gap,
      };
    }

    function isMobile() {
      return layout.isMobile;
    }

    function currentCardWidth() {
      return layout.cardWidth;
    }

    function currentSlotWidth() {
      return layout.slotWidth;
    }

    for (let copy = 0; copy < COPIES; copy++) {
      items.forEach((item, originalIndex) => {
        const card = item.template.cloneNode(true);
        const image = card.querySelector("img");

        card.dataset.index = originalIndex;
        if (copy !== 1) card.setAttribute("aria-hidden", "true");

        if (image) {
          image.draggable = false;
          image.addEventListener("error", () => {
            console.error(
              `Image could not be loaded: ${image.getAttribute("src")}`,
            );
          });
        }

        track.appendChild(card);
        allCards.push({ el: card, origIdx: originalIndex });
      });
    }

    function sizeAt(distSlots) {
      if (isMobile()) {
        return {
          w: currentCardWidth(),
          h: MOBILE_CARD_H,
        };
      }

      const distance = Math.abs(distSlots);
      const maxDistance = Math.floor(N / 2) + 1;
      let height;

      if (distance <= 1) {
        height = CENTER_H + (NEXT_H - CENTER_H) * distance;
      } else {
        const progress = Math.min(
          (distance - 1) / Math.max(maxDistance - 1, 1),
          1,
        );
        height = NEXT_H + (MAX_H - NEXT_H) * progress * progress;
      }

      const widthProgress = Math.min(distance, maxDistance) / maxDistance;
      const width =
        CENTER_W + (MAX_W - CENTER_W) * widthProgress * widthProgress;

      return { w: width, h: height };
    }

    function viewportWidth() {
      return layout.viewportWidth;
    }

    function calculateOffset(index) {
      const slotWidth = currentSlotWidth();

      if (isMobile()) {
        return index * slotWidth - MOBILE_SIDE_SPACE;
      }

      return index * slotWidth + slotWidth / 2 - viewportWidth() / 2;
    }

    function setupSizes() {
      refreshLayout();
      const slotWidth = currentSlotWidth();

      viewport.style.setProperty(
        "--mobile-side-space",
        `${MOBILE_SIDE_SPACE}px`,
      );
      viewport.style.height = isMobile() ? `${MOBILE_CARD_H}px` : `${MAX_H}px`;
      track.style.width = `${TOTAL * slotWidth}px`;

      allCards.forEach(({ el }, index) => {
        el.style.left = `${index * slotWidth}px`;
        el.style.width = `${currentCardWidth()}px`;

        if (isMobile()) {
          el.style.height = `${MOBILE_CARD_H}px`;
          el.style.opacity = "1";
          el.style.borderRadius = `${MOBILE_RADIUS}px`;
        } else {
          el.style.borderRadius = "";
        }
      });
    }

    refreshLayout();

    let activeIndex = isMobile() ? N : N + Math.floor(N / 2);
    let offset = calculateOffset(activeIndex);
    let snapTarget = offset;
    let isDragging = false;
    let hasDragged = false;
    let dragStartX = 0;
    let dragStartOffset = 0;
    let dragLastX = 0;
    let dragLastT = 0;
    let velocity = 0;
    let activeContentIndex = -1;
    let animationFrameId = 0;

    function wrapOffset() {
      const setWidth = N * currentSlotWidth();
      const minIndex = N;
      const maxIndex = N * 2 - 1;

      if (activeIndex < minIndex) {
        activeIndex += N;
        offset += setWidth;
        snapTarget += setWidth;
      }

      if (activeIndex > maxIndex) {
        activeIndex -= N;
        offset -= setWidth;
        snapTarget -= setWidth;
      }
    }

    function updateCarouselUi() {
      const originalIndex = ((activeIndex % N) + N) % N;
      const activeItem = items[originalIndex];

      if (activeContentIndex === originalIndex) {
        return;
      }

      if (activeSlideTitle) {
        activeSlideTitle.textContent = activeItem.label;
      }

      if (activeSlideDescription) {
        activeSlideDescription.textContent = activeItem.description;
      }

      dots.forEach((dot, index) => {
        const isActive = index === originalIndex;
        dot.classList.toggle("h-3", isActive);
        dot.classList.toggle("w-3", isActive);
        dot.classList.toggle("h-2", !isActive);
        dot.classList.toggle("w-2", !isActive);
        dot.classList.toggle("bg-[#CBFF00]", isActive);
        dot.classList.toggle("bg-gray-500", !isActive);
        dot.setAttribute("aria-current", isActive ? "true" : "false");
      });

      activeContentIndex = originalIndex;
    }

    function renderFrame() {
      const slotWidth = currentSlotWidth();
      const translateX = -offset;
      const viewportCenter = layout.viewportCenter;

      track.style.transform = `translate3d(${translateX}px, 0, 0)`;
      updateCarouselUi();

      if (isMobile()) {
        return;
      }

      allCards.forEach(({ el }, index) => {
        const slotCenter = index * slotWidth + slotWidth / 2 + translateX;
        const distancePixels = slotCenter - viewportCenter;
        const distanceSlots = distancePixels / slotWidth;
        const { h } = sizeAt(distanceSlots);
        const absoluteDistance = Math.abs(distanceSlots);
        const maxVisible = N / 2 + 0.5;
        const opacity = Math.max(
          0.15,
          1 - (absoluteDistance / maxVisible) * 0.75,
        );

        el.style.height = `${h}px`;
        el.style.opacity = opacity;
      });
    }

    function indexFromOffset(value = offset) {
      const slotWidth = currentSlotWidth();

      if (isMobile()) {
        return Math.round((value + MOBILE_SIDE_SPACE) / slotWidth);
      }

      return Math.round(
        (value + viewportWidth() / 2 - slotWidth / 2) / slotWidth,
      );
    }

    function snapToIndex(index) {
      activeIndex = index;
      snapTarget = calculateOffset(activeIndex);
    }

    function snapNearest(direction = 0) {
      if (direction === 1) {
        activeIndex += 1;
      } else if (direction === -1) {
        activeIndex -= 1;
      } else {
        activeIndex = indexFromOffset();
      }

      snapTarget = calculateOffset(activeIndex);
    }

    function dragDirection(totalMovement) {
      if (velocity > 0.3) return 1;
      if (velocity < -0.3) return -1;
      if (totalMovement > 40) return 1;
      if (totalMovement < -40) return -1;
      return 0;
    }

    function settleDrag(endX) {
      const totalMovement = dragStartX - endX;
      const direction = dragDirection(totalMovement);

      if (direction === 0) {
        snapNearest(0);
        return;
      }

      const nearestIndex = indexFromOffset();
      snapToIndex(
        nearestIndex === activeIndex ? activeIndex + direction : nearestIndex,
      );
    }

    function requestAnimationTick() {
      if (animationFrameId === 0) {
        animationFrameId = requestAnimationFrame(animationLoop);
      }
    }

    function animationLoop() {
      animationFrameId = 0;
      let shouldContinue = isDragging;

      if (!isDragging) {
        const difference = snapTarget - offset;

        if (Math.abs(difference) > 0.2) {
          offset += difference * SNAP_EASE;
          shouldContinue = true;
        } else {
          offset = snapTarget;
        }

        wrapOffset();
      }

      renderFrame();

      if (shouldContinue) {
        requestAnimationTick();
      }
    }

    viewport.addEventListener("mousedown", (event) => {
      isDragging = true;
      hasDragged = false;
      dragStartX = event.clientX;
      dragStartOffset = offset;
      dragLastX = event.clientX;
      dragLastT = performance.now();
      velocity = 0;
      viewport.classList.add("dragging");
      requestAnimationTick();
      event.preventDefault();
    });

    window.addEventListener("mousemove", (event) => {
      if (!isDragging) return;

      const movement = dragStartX - event.clientX;
      if (Math.abs(movement) > 5) hasDragged = true;

      const deltaX = dragLastX - event.clientX;
      const deltaTime = performance.now() - dragLastT;

      velocity = deltaX / Math.max(deltaTime, 1);
      offset = dragStartOffset + movement;
      dragLastX = event.clientX;
      dragLastT = performance.now();

      wrapOffset();
      requestAnimationTick();
    });

    window.addEventListener("mouseup", (event) => {
      if (!isDragging) return;

      isDragging = false;
      viewport.classList.remove("dragging");

      settleDrag(event.clientX);
      requestAnimationTick();
    });

    viewport.addEventListener(
      "touchstart",
      (event) => {
        if (!event.touches.length) return;

        isDragging = true;
        hasDragged = false;
        dragStartX = event.touches[0].clientX;
        dragStartOffset = offset;
        dragLastX = dragStartX;
        dragLastT = performance.now();
        velocity = 0;
        requestAnimationTick();
      },
      { passive: true },
    );

    window.addEventListener(
      "touchmove",
      (event) => {
        if (!isDragging || !event.touches.length) return;

        const currentX = event.touches[0].clientX;
        const movement = dragStartX - currentX;

        if (Math.abs(movement) > 5) hasDragged = true;

        const deltaX = dragLastX - currentX;
        const deltaTime = performance.now() - dragLastT;

        velocity = deltaX / Math.max(deltaTime, 1);
        offset = dragStartOffset + movement;
        dragLastX = currentX;
        dragLastT = performance.now();

        wrapOffset();
        requestAnimationTick();
      },
      { passive: true },
    );

    window.addEventListener("touchend", (event) => {
      if (!isDragging) return;

      isDragging = false;

      const endX = event.changedTouches[0]?.clientX ?? dragLastX;
      settleDrag(endX);
      requestAnimationTick();
    });

    window.addEventListener("touchcancel", () => {
      isDragging = false;
      snapNearest();
      requestAnimationTick();
    });

    allCards.forEach(({ el }, index) => {
      el.addEventListener("click", () => {
        if (hasDragged) return;

        activeIndex = index;
        snapTarget = calculateOffset(activeIndex);
        wrapOffset();
        requestAnimationTick();
      });
    });

    viewport.addEventListener("keydown", (event) => {
      if (["ArrowLeft", "ArrowRight"].includes(event.key)) event.preventDefault();
      if (event.key === "ArrowLeft") {
        snapNearest(-1);
        requestAnimationTick();
      }

      if (event.key === "ArrowRight") {
        snapNearest(1);
        requestAnimationTick();
      }
    });

    let wasMobile = isMobile();

    window.addEventListener("resize", () => {
      const nowMobile = window.innerWidth <= MOBILE_BREAKPOINT;

      if (nowMobile && !wasMobile) {
        activeIndex = N;
      }

      setupSizes();
      snapTarget = calculateOffset(activeIndex);
      offset = snapTarget;
      renderFrame();

      wasMobile = nowMobile;
    });

    setupSizes();
    renderFrame();
  }
}

}
