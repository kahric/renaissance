import {Swiper} from "swiper";
import {Navigation, Pagination, Thumbs} from "swiper/modules";

export const registerSwiper = () =>
{
    const mainSwiper = new Swiper(".mySwiper", {
        modules: [Navigation, Pagination],
        spaceBetween: 10,
        slidesPerView: 4,
        freeMode: true,
        watchSlidesProgress: true,
    });
    const thumbnailSwuper = new Swiper(".mySwiper2", {
        modules: [Navigation, Thumbs],
        spaceBetween: 10,
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        thumbs: {
            swiper: mainSwiper,
        },
    });
}