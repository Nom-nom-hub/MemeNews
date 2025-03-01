<?php
// Include configuration
require_once 'config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meme Preview Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
        }
        .image-test {
            margin: 20px 0;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #f9f9f9;
        }
        .image-test h2 {
            margin-top: 0;
            color: #666;
        }
        img {
            max-width: 100%;
            display: block;
            margin: 0 auto;
            border: 1px solid #eee;
        }
        .test-controls {
            margin-top: 15px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        button {
            background-color: #4F46E5;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
        }
        button:hover {
            background-color: #3730A3;
        }
        .success {
            background-color: #10B981;
        }
        .success:hover {
            background-color: #059669;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Meme Preview Image Test</h1>
        
        <div class="image-test">
            <h2>SVG Placeholder Animation Test</h2>
            <img src="<?php echo SITE_URL; ?>/img/placeholder.svg" alt="Placeholder Animation">
            <p>This should show an animated SVG placeholder. If you see a broken image, the file might be missing or there could be an SVG compatibility issue.</p>
            
            <div class="test-controls">
                <button onclick="testSVG()">Test SVG Support</button>
            </div>
        </div>
        
        <div class="image-test">
            <h2>Sample Template Test</h2>
            <img src="<?php echo SITE_URL; ?>/img/templates/change-my-mind.jpg" alt="Change My Mind Template">
            <p>This should show a sample template. If you see a broken image, the file might be missing.</p>
            
            <div class="test-controls">
                <button onclick="swapImages()">Swap Placeholder with Template</button>
                <button class="success" onclick="goToMemeGenerator()">Go to Meme Generator</button>
            </div>
        </div>
    </div>
    
    <script>
        function testSVG() {
            // Test for SVG support
            const SVG_NS = 'http://www.w3.org/2000/svg';
            const svgSupported = !!document.createElementNS && !!document.createElementNS(SVG_NS, 'svg').createSVGRect;
            
            if (svgSupported) {
                alert('Great! Your browser supports SVG animations.');
            } else {
                alert('Your browser might not fully support SVG animations. Consider updating your browser.');
            }
        }
        
        function swapImages() {
            const images = document.querySelectorAll('.image-test img');
            const src1 = images[0].src;
            const src2 = images[1].src;
            const alt1 = images[0].alt;
            const alt2 = images[1].alt;
            
            images[0].src = src2;
            images[0].alt = alt2;
            images[1].src = src1;
            images[1].alt = alt1;
        }
        
        function goToMemeGenerator() {
            window.location.href = '<?php echo SITE_URL; ?>?page=generate';
        }
    </script>
</body>
</html> 