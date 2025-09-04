<?php
require_once 'config/website_config.php';

// Lấy cấu hình website
$config = getWebsiteConfig();

// Hàm tạo thời gian phản hồi ngẫu nhiên
function getRandomResponseTime() {
    return rand(20, 60);
}
?>
<!DOCTYPE html>
<html style="font-size: 100px;">
<head design-width="750">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="telephone=no">
    <title><?php echo $config['title']; ?></title>
    <link rel="icon" href="<?php echo $config['favicon']; ?>">
    <link rel="stylesheet" href="asset/css/style2.min.css">
    <link rel="stylesheet" href="asset/css/Swiper.css">
    <link rel="stylesheet" href="asset/css/animate.min.css">
    <script src="asset/js/rem.js"></script>
    <style>
        body {
            <?php if ($config['background_type'] === 'image'): ?>
            background-image: url('<?php echo $config['background']; ?>');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            <?php else: ?>
            background-color: <?php echo $config['background_color']; ?>;
            <?php endif; ?>
        }
        .mobile-wrap {
            background-color: <?php echo $config['container_bg_color']; ?>;
        }
                 .tops {
             text-align: center;
         }
         .tops img {
             width: <?php echo $config['logo_size']; ?>%;
             height: auto;
             display: inline-block;
         }
         .marquee {
             color: <?php echo $config['marquee_color']; ?> !important;
         }
        @media screen and (min-width:750px) {
            .center {
                width: 750px !important;
                margin-left: -375px !important;
                left: 50% !important;
            }
            .fixed-right {
                right: calc((100% - 750px)/2)
            }
        }
    </style>
</head>

<body>
    <div class="mobile-wrap center">
        <div class="tops">
            <img src="<?php echo $config['logo']; ?>" alt="8Qbet">
        </div>
        <div class="container">
            <!-- Banner Slider -->
            <div class="swiper-container bann swiper-container-horizontal">
                <ul id="swiper-wrapper" class="swiper-wrapper">
                    <?php foreach($config['banners'] as $index => $banner): ?>
                    <li class="swiper-slide" data-swiper-slide-index="<?php echo $index; ?>" style="width: 710px;">
                        <img src="<?php echo $banner; ?>" alt="8Qbet">
                    </li>
                    <?php endforeach; ?>
                </ul>
                <div class="swiper-pagination swiper-pagination-clickable swiper-pagination-bullets">
                    <?php for($i = 0; $i < count($config['banners']); $i++): ?>
                    <span class="swiper-pagination-bullet" tabindex="0" role="button" aria-label="Go to slide <?php echo $i+1; ?>"></span>
                    <?php endfor; ?>
                </div>
                <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>
            </div>

            <!-- Notice Marquee -->
            <div class="notice">
                <div class="ico fl">
                    <img src="asset/images/notice-icon.png" alt="8Qbet">
                </div>
                <div id="marquee" class="marquee fr">
                    <marquee scrollamount="3"><?php echo $config['marquee']; ?></marquee>
                </div>
            </div>

            <!-- Links List -->
            <div id="lines" class="lines">
                <?php foreach($config['links'] as $link): ?>
                <div class="item">
                    <a href="<?php echo $link['jumpUrl']; ?>" target="_blank" rel="noopener noreferrer">
                        <div class="ico fl">
                            <img src="<?php echo $link['icon']; ?>" alt="8Qbet">
                        </div>
                        <div class="txts fl">
                            <?php echo $link['name']; ?>
                        </div>
                        <div class="ms fl"><?php echo getRandomResponseTime(); ?>ms</div>
                    </a>
                    <div class="webs fl">
                        <a href="<?php echo $link['jumpUrl']; ?>" target="_blank" rel="noopener noreferrer">
                            <?php echo $link['showUrl']; ?>
                        </a>
                    </div>
                    <div class="btns fr">
                        <a href="<?php echo $link['jumpUrl']; ?>" target="_blank" rel="noopener noreferrer">
                            <button>ĐI VÀO</button>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Refresh Button -->
            <div class="refresh">
                <!-- <button onclick="ms()">KIỂM TRA LẠI</button> -->
            </div>

            <!-- Function Buttons -->
            <div class="function">
                <div class="item">
                    <a href="<?php echo $config['serviceUrl']; ?>" target="_blank" rel="noopener noreferrer">
                        <img src="asset/images/kefu.png" alt="8Qbet">
                    </a>
                </div>
                <div class="item">
                    <a href="<?php echo $config['appUrl']; ?>" target="_blank" rel="noopener noreferrer">
                        <img src="asset/images/appxz.png" alt="8Qbet">
                    </a>
                </div>
            </div>
        </div>

                 <!-- Footer -->
         <div class="footer">
             <a href="<?php echo $config['footer_link']; ?>" target="_blank" rel="noopener noreferrer">
                 <img src="<?php echo $config['footer_image']; ?>" alt="8Qbet">
             </a>
         </div>
    </div>

    <!-- Scripts -->
    <script src="asset/js/jquery-2.2.4.min.js"></script>
    <script src="asset/js/swiper-4.2.0.min.js"></script>
    <script src="asset/js/MobEpp-1.1.1.js"></script>
    <script>
        $(document).ready(function () {
            // Initialize Swiper
            var swiper = new Swiper('.bann', {
                pagination: {
                    el: '.bann .swiper-pagination',
                    clickable: true,
                },
                loop: true,
                autoplay: {
                    delay: 3000,
                    disableOnInteraction: false,
                },
            });
        });

        // Random response time function
        function getRandomInt(min, max) {
            min = Math.ceil(min);
            max = Math.floor(max);
            return Math.floor(Math.random() * (max - min + 1)) + min;
        }

        function ms() {
            $('.container .lines .item .ms').removeClass('v1');
            $('.container .lines .item .ms').removeClass('v2');
            $('.container .lines .item').each(function () {
                var m = $(this).index();
                var randomNumber;
                
                if (m % 3 == 0) {
                    randomNumber = getRandomInt(20, 60);
                } else {
                    randomNumber = getRandomInt(10, 50);
                }
                
                $(this).find('.ms').text(randomNumber + 'ms');
                
                if (randomNumber >= 55) {
                    $(this).find('.ms').addClass('v2');
                } else if (randomNumber >= 50) {
                    $(this).find('.ms').addClass('v1');
                }
            });
        }
        
        // Initialize on page load
        ms();
    </script>
</body>
</html>
