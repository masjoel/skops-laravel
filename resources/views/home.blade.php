@extends('layouts.app')

@section('title', 'Home - App Name')

@section('content')
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300">
        <!-- Header -->
        <header class="bg-white dark:bg-gray-800 shadow-md">
            <div class="container mx-auto px-4 py-4 flex justify-between items-center">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="ml-2 text-xl font-bold">App Name</span>
                </div>
                <div class="flex items-center space-x-4">
                    <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md transition-colors duration-200">
                        Login
                    </button>
                    <button id="theme-toggle" class="bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-4 py-2 rounded-md transition-colors duration-200">
                        <i class="fas fa-moon"></i>
                    </button>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="bg-indigo-600 dark:bg-indigo-800 text-white py-16">
            <div class="container mx-auto px-4">
                <div class="flex flex-col md:flex-row items-center justify-between">
                    <div class="md:w-1/2 mb-8 md:mb-0">
                        <h1 class="text-4xl md:text-5xl font-bold mb-4">Welcome to App Name</h1>
                        <p class="text-lg mb-6">Discover the power of modern web development with our application.</p>
                        <button class="bg-white text-indigo-600 hover:bg-indigo-100 px-6 py-3 rounded-md transition-colors duration-200">
                            Get Started
                        </button>
                    </div>
                    <div class="md:w-1/2">
                        <div class="relative">
                            <img src="https://via.placeholder.com/600x400" alt="Hero Image" class="rounded-lg shadow-lg">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-12">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold text-center mb-12">Features</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 transition-all duration-300 hover:shadow-lg">
                        <i class="fas fa-lightbulb text-indigo-600 dark:text-indigo-400 mb-4"></i>
                        <h3 class="text-xl font-semibold mb-2">Innovative Design</h3>
                        <p class="text-gray-600 dark:text-gray-300">We use modern design principles to create beautiful and functional interfaces.</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 transition-all duration-300 hover:shadow-lg">
                        <i class="fas fa-chart-line text-indigo-600 dark:text-indigo-400 mb-4"></i>
                        <h3 class="text-xl font-semibold mb-2">Performance Optimization</h3>
                        <p class="text-gray-600 dark:text-gray-300">Our application is optimized for speed and efficiency, ensuring a smooth user experience.</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 transition-all duration-300 hover:shadow-lg">
                        <i class="fas fa-shield-alt text-indigo-600 dark:text-indigo-400 mb-4"></i>
                        <h3 class="text-xl font-semibold mb-2">Security First</h3>
                        <p class="text-gray-600 dark:text-gray-300">We prioritize security to protect your data and ensure a safe environment.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Carousel Section -->
        <section class="py-12">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold text-center mb-12">Our Carousel</h2>
                <div class="relative">
                    <div id="carousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="https://via.placeholder.com/800x400" class="d-block w-100" alt="Slide 1">
                            </div>
                            <div class="carousel-item">
                                <img src="https://via.placeholder.com/800x400" class="d-block w-100" alt="Slide 2">
                            </div>
                            <div class="carousel-item">
                                <img src="https://via.placeholder.com/800x400" class="d-block w-100" alt="Slide 3">
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Cards Section -->
        <section class="py-12">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold text-center mb-12">Our Services</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 transition-all duration-300 hover:shadow-lg">
                        <i class="fas fa-code text-indigo-600 dark:text-indigo-400 mb-4"></i>
                        <h3 class="text-xl font-semibold mb-2">Web Development</h3>
                        <p class="text-gray-600 dark:text-gray-300">We build modern, responsive, and scalable web applications tailored to your needs.</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 transition-all duration-300 hover:shadow-lg">
                        <i class="fas fa-mobile-alt text-indigo-600 dark:text-indigo-400 mb-4"></i>
                        <h3 class="text-xl font-semibold mb-2">Mobile App Development</h3>
                        <p class="text-gray-600 dark:text-gray-300">We create cross-platform mobile applications that work seamlessly on both iOS and Android.</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 transition-all duration-300 hover:shadow-lg">
                        <i class="fas fa-server text-indigo-600 dark:text-indigo-400 mb-4"></i>
                        <h3 class="text-xl font-semibold mb-2">Backend Development</h3>
                        <p class="text-gray-600 dark:text-gray-300">We build robust and scalable backend systems using the latest technologies and best practices.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-800 dark:bg-gray-900 text-white py-8">
            <div class="container mx-auto px-4">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="mb-4 md:mb-0">
                        <h3 class="text-xl font-bold">App Name</h3>
                        <p class="text-gray-400">Discover the power of modern web development.</p>
                    </div>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="mt-6 text-center text-gray-400">
                    <p>&copy; {{ date('Y') }} App Name. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>
@endsection