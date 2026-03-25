<script src="assets/js/popup.js"></script>

<!-- Nút lên đầu trang -->
<button id="backToTop" title="Lên đầu trang">▲</button>

<style>
#backToTop {
    position: fixed; bottom: 30px; right: 30px;
    width: 45px; height: 45px; background: #2c1a0e;
    color: #f0e6d3; border: none; border-radius: 50%;
    font-size: 18px; cursor: pointer; display: none;
    z-index: 998; box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    transition: all 0.3s;
}
#backToTop:hover { background: #c9a96e; transform: translateY(-3px); }
</style>

<script>
// Logic 
const backToTopBtn = document.getElementById('backToTop');
window.addEventListener('scroll', () => {
    backToTopBtn.style.display = window.scrollY > 300 ? 'block' : 'none';
});
backToTopBtn.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
});
</script>