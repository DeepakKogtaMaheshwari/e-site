<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Product 1: Ultra Precision Mouse
        $product1 = new Product();
        $product1->setName('B2Battle Precision X1 Ultra Computer Mouse');
        $product1->setDescription('🖱️ Professional computer mouse with 30,000 DPI PixArt sensor, 0.2ms response time, and lightweight 59g design. Features RGB lighting, magnetic scroll wheel, and premium PTFE feet. Perfect for professionals, designers, and productivity enthusiasts. Precision that enhances your workflow.');
        $product1->setPrice('5999.00');
        $product1->setImageUrl('https://images.unsplash.com/photo-1527814050087-3793815479db?w=500&h=500&fit=crop&auto=format');
        $manager->persist($product1);

        // Product 2: Mechanical Keyboard
        $product2 = new Product();
        $product2->setName('B2Battle Professional Mechanical Keyboard RGB');
        $product2->setDescription('⌨️ Professional-grade mechanical keyboard with custom Cherry MX switches, per-key RGB lighting, and aircraft-grade aluminum frame. Features 1ms polling rate, N-key rollover, and programmable macro keys. Hot-swappable switches for ultimate customization. Perfect for professionals and enthusiasts.');
        $product2->setPrice('12999.00');
        $product2->setImageUrl('https://images.unsplash.com/photo-1541140532154-b024d705b90a?w=500&h=500&fit=crop&auto=format');
        $manager->persist($product2);

        // Product 3: Professional Headset
        $product3 = new Product();
        $product3->setName('B2Battle Professional Audio Headset');
        $product3->setDescription('🎧 Professional 7.1 surround sound headset with 50mm titanium drivers, AI-powered noise cancellation, and crystal-clear boom mic. Features memory foam padding, 20-hour battery life, and multi-platform compatibility. Perfect for work calls, music, and entertainment. Professional audio quality.');
        $product3->setPrice('8999.00');
        $product3->setImageUrl('https://images.unsplash.com/photo-1599669454699-248893623440?w=500&h=500&fit=crop&auto=format');
        $manager->persist($product3);

        // Product 4: Professional Monitor
        $product4 = new Product();
        $product4->setName('B2Battle Professional 27" 240Hz Monitor');
        $product4->setDescription('🖥️ Ultra-fast 27" QHD professional monitor with 240Hz refresh rate, 0.5ms response time, and HDR1000 support. Features NVIDIA G-Sync, quantum dot technology, and height-adjustable stand. Experience smooth performance with vibrant colors. Perfect for professionals and content creators.');
        $product4->setPrice('34999.00');
        $product4->setImageUrl('https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=500&h=500&fit=crop&auto=format');
        $manager->persist($product4);

        // Product 5: Ergonomic Office Chair
        $product5 = new Product();
        $product5->setName('B2Battle Elite Ergonomic Office Chair');
        $product5->setDescription('🪑 Ergonomic office chair with 4D armrests, memory foam lumbar support, and premium PU leather. Features 180° recline, 360° swivel, and supports up to 180kg. Built for long work sessions with superior comfort and style. Perfect for professionals who value comfort.');
        $product5->setPrice('24999.00');
        $product5->setImageUrl('https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=500&h=500&fit=crop&auto=format');
        $manager->persist($product5);

        // Product 6: Wireless Controller
        $product6 = new Product();
        $product6->setName('B2Battle Elite Wireless Controller');
        $product6->setDescription('🎮 Professional wireless controller with Hall Effect triggers, anti-drift sticks, and 50-hour battery life. Features customizable RGB, programmable buttons, and advanced button mapping. Compatible with PC, Xbox, and mobile devices. Perfect for entertainment and productivity.');
        $product6->setPrice('9999.00');
        $product6->setImageUrl('https://images.unsplash.com/photo-1606144042614-b2417e99c4e3?w=500&h=500&fit=crop&auto=format');
        $manager->persist($product6);

        // Product 7: High-Performance Laptop
        $product7 = new Product();
        $product7->setName('B2Battle Professional Laptop RTX 4080');
        $product7->setDescription('💻 High-performance laptop with Intel i9-13900HX, RTX 4080, 32GB DDR5, 1TB NVMe SSD. Features 17.3" 240Hz display, mechanical keyboard, and advanced cooling system. Portable powerhouse for professionals, creators, and power users. Desktop-level performance on the go.');
        $product7->setPrice('189999.00');
        $product7->setImageUrl('https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=500&h=500&fit=crop&auto=format');
        $manager->persist($product7);

        // Product 8: Gaming Mousepad
        $product8 = new Product();
        $product8->setName('B2Battle Arena XXL Gaming Mousepad');
        $product8->setDescription('🖱️ Extended gaming surface with micro-textured cloth top and anti-slip rubber base. Features RGB edge lighting, water-resistant coating, and premium stitched edges. Size: 900x400x4mm. Perfect for low-sens gaming and full desk coverage. The foundation of every pro setup.');
        $product8->setPrice('2999.00');
        $product8->setImageUrl('https://images.unsplash.com/photo-1625842268584-8f3296236761?w=500&h=500&fit=crop&auto=format');
        $manager->persist($product8);

        // Product 9: Webcam
        $product9 = new Product();
        $product9->setName('B2Battle StreamCam Pro 4K');
        $product9->setDescription('📹 Professional 4K streaming camera with auto-focus, HDR, and AI-powered background removal. Features 60fps recording, built-in microphone, and plug-and-play setup. Perfect for streaming, content creation, and video calls. Look like a pro, stream like a champion.');
        $product9->setPrice('15999.00');
        $product9->setImageUrl('https://images.unsplash.com/photo-1587825140708-dfaf72ae4b04?w=500&h=500&fit=crop&auto=format');
        $manager->persist($product9);

        // Product 10: VR Headset
        $product10 = new Product();
        $product10->setName('B2Battle VR Elite Headset');
        $product10->setDescription('🥽 Next-gen VR headset with 4K per eye resolution, 120Hz refresh rate, and inside-out tracking. Features wireless connectivity, 6DOF controllers, and 3-hour battery life. Experience gaming in a whole new dimension. The future of gaming is here, and it\'s immersive.');
        $product10->setPrice('45999.00');
        $product10->setImageUrl('https://images.unsplash.com/photo-1622979135225-d2ba269cf1ac?w=500&h=500&fit=crop&auto=format');
        $manager->persist($product10);

        // Product 11: Gaming Desk
        $product11 = new Product();
        $product11->setName('B2Battle Command Center Gaming Desk');
        $product11->setDescription('🖥️ Ultimate gaming command center with RGB lighting, cable management, cup holder, and headphone hook. Features carbon fiber surface, adjustable height, and supports triple monitor setup. Built for marathon gaming sessions. Your battle station awaits.');
        $product11->setPrice('19999.00');
        $product11->setImageUrl('https://images.unsplash.com/photo-1541558869434-2840d308329a?w=500&h=500&fit=crop&auto=format');
        $manager->persist($product11);

        // Product 12: Mechanical Switches
        $product12 = new Product();
        $product12->setName('B2Battle Custom Mechanical Switches (90pk)');
        $product12->setDescription('⌨️ Premium tactile switches for ultimate customization. Features gold-plated contacts, 50M keystroke lifespan, and satisfying click feedback. Compatible with hot-swap keyboards. Elevate your typing experience to pro level.');
        $product12->setPrice('3999.00');
        $product12->setImageUrl('https://images.unsplash.com/photo-1595044426077-d36d9236d54a?w=500&h=500&fit=crop&auto=format');
        $manager->persist($product12);

        // Product 13: RGB Light Strips
        $product13 = new Product();
        $product13->setName('B2Battle RGB Gaming Light Kit');
        $product13->setDescription('💡 Immersive RGB lighting system with 16.8M colors, music sync, and app control. Features adhesive strips, corner connectors, and smart home integration. Transform your gaming space into a neon battleground.');
        $product13->setPrice('4999.00');
        $product13->setImageUrl('https://images.unsplash.com/photo-1518709268805-4e9042af2176?w=500&h=500&fit=crop&auto=format');
        $manager->persist($product13);

        // Product 14: Gaming Speakers
        $product14 = new Product();
        $product14->setName('B2Battle Thunder 2.1 Gaming Speakers');
        $product14->setDescription('🔊 Powerful 2.1 speaker system with deep bass, RGB lighting, and Bluetooth connectivity. Features 100W total power, wooden drivers, and gaming mode EQ. Feel every explosion, hear every footstep.');
        $product14->setPrice('11999.00');
        $product14->setImageUrl('https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=500&h=500&fit=crop&auto=format');
        $manager->persist($product14);

        // Product 15: Gaming Phone
        $product15 = new Product();
        $product15->setName('B2Battle Mobile Gaming Beast');
        $product15->setDescription('📱 Ultimate mobile gaming phone with Snapdragon 8 Gen 3, 16GB RAM, 512GB storage, and 165Hz display. Features shoulder triggers, cooling fan, and 6000mAh battery. Dominate mobile esports anywhere.');
        $product15->setPrice('79999.00');
        $product15->setImageUrl('https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=500&fit=crop&auto=format');
        $manager->persist($product15);

        // Product 16: Capture Card
        $product16 = new Product();
        $product16->setName('B2Battle StreamPro 4K Capture Card');
        $product16->setDescription('🎬 Professional 4K60 capture card for streaming and recording. Features zero-lag passthrough, HDR support, and plug-and-play setup. Stream like a pro, capture every epic moment.');
        $product16->setPrice('21999.00');
        $product16->setImageUrl('https://images.unsplash.com/photo-1587825140708-dfaf72ae4b04?w=500&h=500&fit=crop&auto=format');
        $manager->persist($product16);

        // Product 17: Gaming Glasses
        $product17 = new Product();
        $product17->setName('B2Battle Pro Gaming Glasses');
        $product17->setDescription('👓 Blue light blocking glasses with anti-glare coating and lightweight titanium frame. Reduces eye strain during long gaming sessions. Features UV protection and stylish design. Protect your vision, extend your gameplay.');
        $product17->setPrice('2999.00');
        $product17->setImageUrl('https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=500&h=500&fit=crop&auto=format');
        $manager->persist($product17);

        // Product 18: Cooling Pad
        $product18 = new Product();
        $product18->setName('B2Battle Laptop Cooling Station');
        $product18->setDescription('❄️ Advanced laptop cooling pad with 6 silent fans, RGB lighting, and adjustable height. Features temperature display, USB hub, and ergonomic design. Keep your laptop cool under pressure.');
        $product18->setPrice('5999.00');
        $product18->setImageUrl('https://images.unsplash.com/photo-1593640408182-31c70c8268f5?w=500&h=500&fit=crop&auto=format');
        $manager->persist($product18);

        // Product 19: Gaming Backpack
        $product19 = new Product();
        $product19->setName('B2Battle Tactical Gaming Backpack');
        $product19->setDescription('🎒 Military-grade gaming backpack with laptop compartment, RGB accents, and anti-theft design. Features USB charging port, water resistance, and modular organization. Carry your arsenal in style.');
        $product19->setPrice('7999.00');
        $product19->setImageUrl('https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=500&h=500&fit=crop&auto=format');
        $manager->persist($product19);

        // Product 20: Streaming Microphone
        $product20 = new Product();
        $product20->setName('B2Battle StreamMic Pro USB');
        $product20->setDescription('🎤 Professional USB microphone with cardioid pattern, pop filter, and shock mount. Features real-time monitoring, mute button, and studio-quality sound. Your voice, crystal clear.');
        $product20->setPrice('8999.00');
        $product20->setImageUrl('https://images.unsplash.com/photo-1590602847861-f357a9332bbc?w=500&h=500&fit=crop&auto=format');
        $manager->persist($product20);

        // Product 21: Gaming SSD
        $product21 = new Product();
        $product21->setName('B2Battle NVMe SSD 2TB Gaming Drive');
        $product21->setDescription('💾 Lightning-fast NVMe SSD with 7000MB/s read speeds, RGB heatsink, and 2TB capacity. Features DirectStorage support, 5-year warranty, and instant game loading. No more waiting, just pure speed.');
        $product21->setPrice('16999.00');
        $product21->setImageUrl('https://images.unsplash.com/photo-1597872200969-2b65d56bd16b?w=500&h=500&fit=crop&auto=format');
        $manager->persist($product21);

        // Product 22: Gaming RAM
        $product22 = new Product();
        $product22->setName('B2Battle DDR5 32GB Gaming RAM Kit');
        $product22->setDescription('🧠 High-performance DDR5 memory with 6000MHz speed, RGB lighting, and low latency. Features XMP 3.0 profiles, aluminum heatspreaders, and lifetime warranty. Unleash your system\'s full potential.');
        $product22->setPrice('13999.00');
        $product22->setImageUrl('https://images.unsplash.com/photo-1591799264318-7e6ef8ddb7ea?w=500&h=500&fit=crop&auto=format');
        $manager->persist($product22);

        // Product 23: Graphics Card
        $product23 = new Product();
        $product23->setName('B2Battle RTX 4070 Super Gaming GPU');
        $product23->setDescription('🎮 Next-gen graphics card with ray tracing, DLSS 3, and 12GB VRAM. Features triple-fan cooling, RGB lighting, and 4K gaming performance. Experience games like never before.');
        $product23->setPrice('59999.00');
        $product23->setImageUrl('https://images.unsplash.com/photo-1591488320449-011701bb6704?w=500&h=500&fit=crop&auto=format');
        $manager->persist($product23);

        // Product 24: Power Supply
        $product24 = new Product();
        $product24->setName('B2Battle 850W Modular PSU');
        $product24->setDescription('⚡ 80+ Gold certified power supply with fully modular cables, silent fan, and 10-year warranty. Features RGB lighting, over-voltage protection, and stable power delivery. Power your dreams.');
        $product24->setPrice('12999.00');
        $product24->setImageUrl('https://images.unsplash.com/photo-1518717758536-85ae29035b6d?w=500&h=500&fit=crop&auto=format');
        $manager->persist($product24);

        // Product 25: Motherboard
        $product25 = new Product();
        $product25->setName('B2Battle Z790 Gaming Motherboard');
        $product25->setDescription('🔧 High-end gaming motherboard with WiFi 7, DDR5 support, and PCIe 5.0. Features RGB lighting, premium audio, and robust VRM design. The foundation of every champion build.');
        $product25->setPrice('24999.00');
        $product25->setImageUrl('https://images.unsplash.com/photo-1555617981-dac3880eac6e?w=500&h=500&fit=crop&auto=format');
        $manager->persist($product25);

        // Product 26: CPU Cooler
        $product26 = new Product();
        $product26->setName('B2Battle AIO Liquid Cooler 360mm');
        $product26->setDescription('❄️ All-in-one liquid cooler with 360mm radiator, RGB fans, and pump. Features low noise operation, easy installation, and superior cooling performance. Keep your CPU ice cold.');
        $product26->setPrice('14999.00');
        $product26->setImageUrl('https://images.unsplash.com/photo-1591488320449-011701bb6704?w=500&h=500&fit=crop&auto=format');
        $manager->persist($product26);

        // Product 27: Gaming Case
        $product27 = new Product();
        $product27->setName('B2Battle Fortress Gaming PC Case');
        $product27->setDescription('🏰 Premium gaming case with tempered glass, RGB lighting, and excellent airflow. Features tool-less installation, cable management, and support for 360mm radiators. Your components deserve a fortress.');
        $product27->setPrice('18999.00');
        $product27->setImageUrl('https://images.unsplash.com/photo-1587202372634-32705e3bf49c?w=500&h=500&fit=crop&auto=format');
        $manager->persist($product27);

        // Product 28: Gaming Router
        $product28 = new Product();
        $product28->setName('B2Battle WiFi 7 Gaming Router');
        $product28->setDescription('📡 Ultra-fast WiFi 7 router with gaming acceleration, QoS prioritization, and mesh support. Features 6GHz band, low latency, and advanced security. Dominate online with zero lag.');
        $product28->setPrice('22999.00');
        $product28->setImageUrl('https://images.unsplash.com/photo-1606904825846-647eb07f5be2?w=500&h=500&fit=crop&auto=format');
        $manager->persist($product28);

        // Product 29: Gaming Tablet
        $product29 = new Product();
        $product29->setName('B2Battle Gaming Tablet Pro');
        $product29->setDescription('📱 High-performance gaming tablet with 120Hz display, Snapdragon 8 Gen 3, and 16GB RAM. Features shoulder triggers, cooling system, and 12-hour battery. Mobile gaming redefined.');
        $product29->setPrice('54999.00');
        $product29->setImageUrl('https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=500&h=500&fit=crop&auto=format');
        $manager->persist($product29);

        // Product 30: Gaming Smartwatch
        $product30 = new Product();
        $product30->setName('B2Battle Gaming Smartwatch');
        $product30->setDescription('⌚ Gaming-focused smartwatch with heart rate monitoring, performance tracking, and RGB notifications. Features 7-day battery, water resistance, and gaming stats. Monitor your performance like a pro.');
        $product30->setPrice('9999.00');
        $product30->setImageUrl('https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=500&h=500&fit=crop&auto=format');
        $manager->persist($product30);

        $manager->flush();
    }
}
