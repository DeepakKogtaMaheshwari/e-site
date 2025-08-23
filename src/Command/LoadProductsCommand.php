<?php

namespace App\Command;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:load-products',
    description: 'Load gaming products into the database',
)]
class LoadProductsCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Clear existing products
        $this->entityManager->createQuery('DELETE FROM App\Entity\Product')->execute();

        $products = [
            ['B2Battle Phantom X1 Ultra Gaming Mouse', '🎯 Next-gen esports weapon with 30,000 DPI PixArt sensor, 0.2ms response time, and weightless 59g design. Features RGB Chroma lighting, magnetic scroll wheel, and tournament-grade PTFE feet. Used by world champions in Valorant, CS2, and Apex Legends. Dominate every headshot with precision that defines legends.', '5999.00', 'https://images.unsplash.com/photo-1527814050087-3793815479db?w=500&h=500&fit=crop&auto=format'],
            
            ['B2Battle Apex Mechanical Keyboard RGB', '⚡ Championship-grade mechanical keyboard with custom Cherry MX Speed switches, per-key RGB lighting, and aircraft-grade aluminum frame. Features 1ms polling rate, N-key rollover, and dedicated macro keys. Hot-swappable switches for ultimate customization. The weapon of choice for esports legends.', '12999.00', 'https://images.unsplash.com/photo-1541140532154-b024d705b90a?w=500&h=500&fit=crop&auto=format'],
            
            ['B2Battle Vortex Pro Gaming Headset', '🎧 Professional 7.1 surround sound headset with 50mm titanium drivers, AI-powered noise cancellation, and crystal-clear boom mic. Features memory foam padding, 20-hour battery life, and multi-platform compatibility. Hear every footstep, dominate every callout. Tournament approved for competitive play.', '8999.00', 'https://images.unsplash.com/photo-1599669454699-248893623440?w=500&h=500&fit=crop&auto=format'],
            
            ['B2Battle Quantum 27" 240Hz Gaming Monitor', '🖥️ Ultra-fast 27" QHD gaming monitor with 240Hz refresh rate, 0.5ms response time, and HDR1000 support. Features NVIDIA G-Sync Ultimate, quantum dot technology, and height-adjustable stand. Experience buttery-smooth gameplay with colors that pop. The display that separates pros from amateurs.', '34999.00', 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=500&h=500&fit=crop&auto=format'],
            
            ['B2Battle Throne Elite Gaming Chair', '🪑 Ergonomic gaming throne with 4D armrests, memory foam lumbar support, and premium PU leather. Features 180° recline, 360° swivel, and supports up to 180kg. Built for marathon gaming sessions with superior comfort and style. The chair that keeps champions comfortable during clutch moments.', '24999.00', 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=500&h=500&fit=crop&auto=format'],
            
            ['B2Battle Elite Wireless Pro Controller', '🎮 Tournament-grade wireless controller with Hall Effect triggers, anti-drift sticks, and 50-hour battery life. Features customizable RGB, hair triggers, and pro-level button mapping. Compatible with PC, Xbox, and mobile. The controller that gives you the edge in every game mode.', '9999.00', 'https://images.unsplash.com/photo-1606144042614-b2417e99c4e3?w=500&h=500&fit=crop&auto=format'],
            
            ['B2Battle Titan Gaming Laptop RTX 4080', '💻 Beast mode activated! Intel i9-13900HX, RTX 4080, 32GB DDR5, 1TB NVMe SSD. Features 17.3" 240Hz display, RGB mechanical keyboard, and advanced cooling system. Portable powerhouse for gaming anywhere. Stream, compete, and dominate with desktop-level performance on the go.', '189999.00', 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=500&h=500&fit=crop&auto=format'],
            
            ['B2Battle Arena XXL Gaming Mousepad', '🖱️ Extended gaming surface with micro-textured cloth top and anti-slip rubber base. Features RGB edge lighting, water-resistant coating, and premium stitched edges. Size: 900x400x4mm. Perfect for low-sens gaming and full desk coverage. The foundation of every pro setup.', '2999.00', 'https://images.unsplash.com/photo-1625842268584-8f3296236761?w=500&h=500&fit=crop&auto=format'],
            
            ['B2Battle StreamCam Pro 4K', '📹 Professional 4K streaming camera with auto-focus, HDR, and AI-powered background removal. Features 60fps recording, built-in microphone, and plug-and-play setup. Perfect for streaming, content creation, and video calls. Look like a pro, stream like a champion.', '15999.00', 'https://images.unsplash.com/photo-1587825140708-dfaf72ae4b04?w=500&h=500&fit=crop&auto=format'],
            
            ['B2Battle VR Elite Headset', '🥽 Next-gen VR headset with 4K per eye resolution, 120Hz refresh rate, and inside-out tracking. Features wireless connectivity, 6DOF controllers, and 3-hour battery life. Experience gaming in a whole new dimension. The future of gaming is here, and it\'s immersive.', '45999.00', 'https://images.unsplash.com/photo-1622979135225-d2ba269cf1ac?w=500&h=500&fit=crop&auto=format'],
            
            ['B2Battle Command Center Gaming Desk', '🖥️ Ultimate gaming command center with RGB lighting, cable management, cup holder, and headphone hook. Features carbon fiber surface, adjustable height, and supports triple monitor setup. Built for marathon gaming sessions. Your battle station awaits.', '19999.00', 'https://images.unsplash.com/photo-1541558869434-2840d308329a?w=500&h=500&fit=crop&auto=format'],
            
            ['B2Battle Custom Mechanical Switches (90pk)', '⌨️ Premium tactile switches for ultimate customization. Features gold-plated contacts, 50M keystroke lifespan, and satisfying click feedback. Compatible with hot-swap keyboards. Elevate your typing experience to pro level.', '3999.00', 'https://images.unsplash.com/photo-1595044426077-d36d9236d54a?w=500&h=500&fit=crop&auto=format'],
            
            ['B2Battle RGB Gaming Light Kit', '💡 Immersive RGB lighting system with 16.8M colors, music sync, and app control. Features adhesive strips, corner connectors, and smart home integration. Transform your gaming space into a neon battleground.', '4999.00', 'https://images.unsplash.com/photo-1518709268805-4e9042af2176?w=500&h=500&fit=crop&auto=format'],
            
            ['B2Battle Thunder 2.1 Gaming Speakers', '🔊 Powerful 2.1 speaker system with deep bass, RGB lighting, and Bluetooth connectivity. Features 100W total power, wooden drivers, and gaming mode EQ. Feel every explosion, hear every footstep.', '11999.00', 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=500&h=500&fit=crop&auto=format'],
            
            ['B2Battle Mobile Gaming Beast', '📱 Ultimate mobile gaming phone with Snapdragon 8 Gen 3, 16GB RAM, 512GB storage, and 165Hz display. Features shoulder triggers, cooling fan, and 6000mAh battery. Dominate mobile esports anywhere.', '79999.00', 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&h=500&fit=crop&auto=format'],
            
            ['B2Battle StreamPro 4K Capture Card', '🎬 Professional 4K60 capture card for streaming and recording. Features zero-lag passthrough, HDR support, and plug-and-play setup. Stream like a pro, capture every epic moment.', '21999.00', 'https://images.unsplash.com/photo-1587825140708-dfaf72ae4b04?w=500&h=500&fit=crop&auto=format'],
            
            ['B2Battle Pro Gaming Glasses', '👓 Blue light blocking glasses with anti-glare coating and lightweight titanium frame. Reduces eye strain during long gaming sessions. Features UV protection and stylish design. Protect your vision, extend your gameplay.', '2999.00', 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=500&h=500&fit=crop&auto=format'],
            
            ['B2Battle Laptop Cooling Station', '❄️ Advanced laptop cooling pad with 6 silent fans, RGB lighting, and adjustable height. Features temperature display, USB hub, and ergonomic design. Keep your laptop cool under pressure.', '5999.00', 'https://images.unsplash.com/photo-1593640408182-31c70c8268f5?w=500&h=500&fit=crop&auto=format'],
            
            ['B2Battle Tactical Gaming Backpack', '🎒 Military-grade gaming backpack with laptop compartment, RGB accents, and anti-theft design. Features USB charging port, water resistance, and modular organization. Carry your arsenal in style.', '7999.00', 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=500&h=500&fit=crop&auto=format'],
            
            ['B2Battle StreamMic Pro USB', '🎤 Professional USB microphone with cardioid pattern, pop filter, and shock mount. Features real-time monitoring, mute button, and studio-quality sound. Your voice, crystal clear.', '8999.00', 'https://images.unsplash.com/photo-1590602847861-f357a9332bbc?w=500&h=500&fit=crop&auto=format'],
        ];

        foreach ($products as $productData) {
            $product = new Product();
            $product->setName($productData[0]);
            $product->setDescription($productData[1]);
            $product->setPrice($productData[2]);
            $product->setImageUrl($productData[3]);
            
            $this->entityManager->persist($product);
        }

        $this->entityManager->flush();

        $io->success('Successfully loaded ' . count($products) . ' gaming products!');
        $io->note('🎮 Your B2Battle gaming arsenal is ready to dominate!');

        return Command::SUCCESS;
    }
}
