function toggleFAQ(element) {
        const content = element.nextElementSibling;
        const arrow = element.querySelector('.arrow');

        if (content.classList.contains('show')) {
            content.classList.remove('show');
            arrow.style.transform = 'rotate(0deg)';
        } else {
            document.querySelectorAll('.faq-content').forEach(el => el.classList.remove('show'));
            document.querySelectorAll('.arrow').forEach(a => a.style.transform = 'rotate(0deg)');
            content.classList.add('show');
            arrow.style.transform = 'rotate(180deg)';
        }
    }