// Highlight the hovered/focused floor and its connector line.
(function () {
  const floors = document.querySelectorAll(".piramida-map-floor");
  const lines = document.querySelectorAll(
    ".piramida-map-lines [data-line]",
  );
  const defaultFloor = "ground";

  function setActive(floor) {
    floors.forEach((link) =>
      link.classList.toggle("is-active", link.dataset.floor === floor),
    );
    lines.forEach((line) =>
      line.classList.toggle("is-active", line.dataset.line === floor),
    );
  }

  floors.forEach((link) => {
    link.addEventListener("mouseenter", () =>
      setActive(link.dataset.floor),
    );
    link.addEventListener("focus", () => setActive(link.dataset.floor));
    link.addEventListener("mouseleave", () => setActive(defaultFloor));
    link.addEventListener("blur", () => setActive(defaultFloor));
  });

  setActive(defaultFloor);
})();

const floorPlan = document.getElementById('floorPlan');
if (floorPlan) {
    const mobile = matchMedia('(max-width: 767px)');
    const layout = () => floorPlan.setAttribute('viewBox', mobile.matches ? '80 0 1440 920' : '0 0 1600 920');
    layout();
    mobile.addEventListener('change', layout);
    requestAnimationFrame(() => floorPlan.classList.add('is-ready'));

    const tooltip = document.getElementById('unit-tooltip');
    let hideTimer;
    const hide = () => { tooltip.hidden = true; };
    const hideSoon = () => { hideTimer = setTimeout(hide, 120); };
    floorPlan.querySelectorAll('[data-status="unavailable"]').forEach(unit => {
        const show = () => {
            clearTimeout(hideTimer);
            tooltip.hidden = false;
            const rect = unit.getBoundingClientRect();
            tooltip.style.left = `${Math.max(12, Math.min(rect.left + rect.width / 2 - tooltip.offsetWidth / 2, innerWidth - tooltip.offsetWidth - 12))}px`;
            tooltip.style.top = `${Math.max(12, Math.min(rect.bottom + 8, innerHeight - tooltip.offsetHeight - 12))}px`;
        };
        unit.addEventListener('pointerenter', show);
        unit.addEventListener('focus', show);
        unit.addEventListener('click', show);
        unit.addEventListener('pointerleave', hideSoon);
        unit.addEventListener('blur', hideSoon);
    });
    tooltip.addEventListener('pointerenter', () => clearTimeout(hideTimer));
    tooltip.addEventListener('pointerleave', hideSoon);
    document.addEventListener('keydown', event => { if (event.key === 'Escape') hide(); });
    window.addEventListener('scroll', hide, { passive: true });
    window.addEventListener('resize', hide);
}
