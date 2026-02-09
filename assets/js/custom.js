document.addEventListener("DOMContentLoaded", function () { AOS.init({ duration: 800, easing: "ease-in-out", once: true, mirror: false }); });
$(function () {
    if ($("#alert-box").length) setTimeout(() => $("#alert-box").fadeOut(), 3000);
    $(window).on("load", () => preloader("hide"));
    $("select").not(".not-searchable").filter(function () {
        return !Array.from(this.classList).some(cls => cls.startsWith("ql-"));
    }).addClass("searchable");
    $(".searchable").each(function () {
        const $t = $(this), ph = $t.data("placeholder") || "Select option", m = $t.prop("multiple");
        $t.select2({ placeholder: ph, allowClear: true, width: "100%", closeOnSelect: !m });
    });
    $(document).on("submit", "form", function () { preloader("show"); setTimeout(() => preloader("hide"), 5000); });
    function flattenMenu(m) { let r = []; $.each(m, function (_, i) { if (i.url) r.push({ title: i.title, url: i.url, icon: i.icon || "" }); if (i.children && i.children.length) r = r.concat(flattenMenu(i.children)); }); return r; }
    const pages = flattenMenu(menu); let active = -1;
    const $m = $("#globalSearchModal"), $i = $("#modalSearchInput"), $r = $("#searchResults");
    function buildRoute(u) { if (openRoutes.includes(u)) return u; if (currentRole === "superadmin" && adminRoutes.includes(u)) return "/pages" + u; if (currentRole === "institute" && instituteRoutes.includes(u)) return "/pages" + u; return u; }
    function openModal() { $m.removeClass("hidden").addClass("flex"); setTimeout(() => $i.focus(), 100); }
    function closeModal() { $m.removeClass("flex").addClass("hidden"); $i.val(""); $r.html('<li class="px-4 py-2 text-ts">Start typing to search...</li>'); active = -1; }
    $(document).on("keydown", function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === "k") { e.preventDefault(); openModal(); }
        else if (e.key === "Escape") { closeModal(); }
    });
    $i.on("input", function () {
        const q = $(this).val().trim().toLowerCase(); active = -1;
        if (!q) { $r.html('<li class="px-4 py-2 text-ts">Start typing to search...</li>'); return; }
        const f = pages.filter(p => p.title.toLowerCase().includes(q));
        if (!f.length) { $r.html('<li class="px-4 py-2 text-ts">No results found.</li>'); return; }
        let h = ""; $.each(f, function (_, p) { const u = buildRoute(p.url); h += `<li class="cursor-pointer px-4 py-2 flex items-center gap-2 hover:bg-accent/10 transition" data-url="${u}">${p.icon ? `<i class="${p.icon} text-accent"></i>` : ""}<span>${p.title}</span></li>`; }); $r.html(h);
    });
    $i.on("keydown", function (e) {
        const $it = $r.find("li[data-url]"); if (!$it.length) return;
        if (e.key === "ArrowDown") { e.preventDefault(); active = (active + 1) % $it.length; $it.removeClass("bg-accent/10").eq(active).addClass("bg-accent/10")[0].scrollIntoView({ block: "nearest", behavior: "smooth" }); }
        else if (e.key === "ArrowUp") { e.preventDefault(); active = (active - 1 + $it.length) % $it.length; $it.removeClass("bg-accent/10").eq(active).addClass("bg-accent/10")[0].scrollIntoView({ block: "nearest", behavior: "smooth" }); }
        else if (e.key === "Enter" && active >= 0) { e.preventDefault(); const u = $($it[active]).data("url"); if (u) window.location.href = u; }
    });
    $r.on("click", "li[data-url]", function () { window.location.href = $(this).data("url"); });
    $(document).on("click", "a", function (e) { const h = $(this).attr("href"); if (h && h !== "#" && !h.startsWith("javascript")) { preloader("show"); setTimeout(() => preloader("hide"), 5000); } });
    window.addEventListener("beforeunload", () => { localStorage.setItem("prevPage", window.location.href); });
    $(document).on("keydown", function (e) {
        if (e.key === "Escape") {
            e.preventDefault(); const a = document.querySelector("[x-data]"); if (a) a.__x.$data.open = false;
            const p = localStorage.getItem("prevPage");
            if (p && p !== window.location.href) window.location.href = p;
            else if (document.referrer) window.location.href = document.referrer;
            else window.history.back();
        }
        if (e.ctrlKey && e.key.toLowerCase() === "p") {
            e.preventDefault(); $(".hidden-on-print").addClass("d-none");
            const pl = $("#printURL").attr("href");
            if (pl) window.location.href = pl; else window.print();
        }
    });
});
