<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{subject}}</title>
    <style>
        /* Reset styles for email clients */
        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
        }
        
        /* Container styles */
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
        }
        
        /* Header styles */
        .email-header {
            text-align: center;
            padding: 20px 0;
            background-color: #f8f9fa;
            border-radius: 5px 5px 0 0;
        }
        
        /* Content styles */
        .email-content {
            padding: 30px 20px;
            background-color: #ffffff;
        }
        
        /* Footer styles */
        .email-footer {
            text-align: center;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 0 0 5px 5px;
            font-size: 12px;
            color: #666666;
        }
        
        /* Button styles */
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #007bff;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 4px;
            margin: 20px 0;
        }
        
        /* Responsive styles */
        @media screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
            }
            
            .email-content {
                padding: 20px 15px !important;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <img src="{{logo_url}}" alt="{{company_name}}" style="max-width: 200px; height: auto;">
        </div>
        
        <div class="email-content">
            <?php if (isset($greeting)): ?>
                <h2><?php echo $greeting; ?></h2>
            <?php else: ?>
                <h2>Beste {{name}},</h2>
            <?php endif; ?>

            <?php if (isset($message)): ?>
                <p><?php echo $message; ?></p>
            <?php endif; ?>

            <?php if (isset($custom_content)): ?>
                <?php echo $custom_content; ?>
            <?php endif; ?>

            <?php if (isset($cta_url) && isset($cta_text)): ?>
                <div style="text-align: center;">
                    <a href="<?php echo $cta_url; ?>" class="button"><?php echo $cta_text; ?></a>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="email-footer">
            <p>
                <?php if (isset($footer_text)): ?>
                    <?php echo $footer_text; ?>
                <?php else: ?>
                    © <?php echo date('Y'); ?> {{company_name}}. Alle rechten voorbehouden.
                <?php endif; ?>
            </p>
        </div>
    </div>
</body>
</html> 