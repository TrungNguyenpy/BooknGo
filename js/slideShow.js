var imgFeature = document.querySelector('.gallery-main-image');
var listImg = document.querySelectorAll('.gallery-thumbnails img');
var prevBtn = document.querySelector('.gallery-control-prev');
var nextBtn = document.querySelector('.gallery-control-next');
var gallery = document.querySelector('.gallery');

var currentIndex = 0;
var autoSlideInterval;

function updateImageByIndex(index) {
    document.querySelectorAll('.gallery-thumbnails div').forEach(item => {
        item.classList.remove('gallery-active');
    });
    currentIndex = index;
    imgFeature.src = listImg[index].getAttribute('src');
    listImg[index].parentElement.classList.add('gallery-active');
}

function changeImageWithEffect(index, direction) {
    imgFeature.classList.remove('gallery-slide-left', 'gallery-slide-right');
    
    if (direction === 'left') {
        imgFeature.classList.add('gallery-slide-left');
    } else if (direction === 'right') {
        imgFeature.classList.add('gallery-slide-right');
    }

    setTimeout(() => {
        updateImageByIndex(index);
        imgFeature.classList.remove('gallery-slide-left', 'gallery-slide-right');
    }, 300);
}

// Prev
prevBtn.addEventListener('click', () => {
    let newIndex = currentIndex === 0 ? listImg.length - 1 : currentIndex - 1;
    changeImageWithEffect(newIndex, 'left');
});

// Next
nextBtn.addEventListener('click', () => {
    let newIndex = currentIndex === listImg.length - 1 ? 0 : currentIndex + 1;
    changeImageWithEffect(newIndex, 'right');
});

// Click thumbnail
listImg.forEach((imgElement, index) => {
    imgElement.addEventListener('click', () => {
        changeImageWithEffect(index, 'right');
    });
});

// Auto slide
function startAutoSlide() {
    autoSlideInterval = setInterval(() => {
        let newIndex = currentIndex === listImg.length - 1 ? 0 : currentIndex + 1;
        changeImageWithEffect(newIndex, 'right');
    }, 3000); // đổi ảnh sau 3 giây
}

function stopAutoSlide() {
    clearInterval(autoSlideInterval);
}

// Tạm dừng khi hover
gallery.addEventListener('mouseenter', stopAutoSlide);
gallery.addEventListener('mouseleave', startAutoSlide);

// Khởi tạo
updateImageByIndex(0);
startAutoSlide();
