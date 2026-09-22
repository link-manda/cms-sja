/*
Template Name: Tailwik - TailwindCSS Admin & Dashboard Template
Author: Themesdesign
Version: 2.0.0
Website: https://themesdesign.in/
Contact: themesdesign.in@gmail.com
File: vendor.js
*/


// import '../css/style.css'

import 'preline';

import 'simplebar';

import { createIcons, icons } from 'lucide';
createIcons({ icons });

// Expose lucide wrapper globally for dynamic Blade components
window.lucide = {
    createIcons: (options = {}) => createIcons({ icons, ...options }),
    icons,
};

import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();