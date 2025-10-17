<?php
/**
 * AIEO Schema Generators for Bluehost
 * 
 * PHP functions for generating JSON-LD schema markup.
 * Works on standard Bluehost shared hosting (no special extensions needed).
 * 
 * Usage:
 *   Include this file in your PHP page and call the appropriate function.
 *   
 * Example:
 *   <?php require_once 'generate_schema.php'; ?>
 *   <?php echo generate_article_schema('My Title', 'Description...', 'Author Name', '2025-01-15'); ?>
 */

/**
 * Generate FAQ Schema
 * 
 * @param array $faqs Array of associative arrays with 'question' and 'answer' keys
 * @return string JSON-LD script tag
 * 
 * Example:
 *   $faqs = [
 *       ['question' => 'What is AIEO?', 'answer' => 'AIEO is...'],
 *       ['question' => 'How does it work?', 'answer' => 'It works by...']
 *   ];
 *   echo generate_faq_schema($faqs);
 */
function generate_faq_schema($faqs) {
    $schema = [
        "@context" => "https://schema.org",
        "@type" => "FAQPage",
        "mainEntity" => []
    ];
    
    foreach ($faqs as $faq) {
        if (!isset($faq['question']) || !isset($faq['answer'])) {
            continue; // Skip invalid entries
        }
        
        $schema["mainEntity"][] = [
            "@type" => "Question",
            "name" => $faq['question'],
            "acceptedAnswer" => [
                "@type" => "Answer",
                "text" => $faq['answer']
            ]
        ];
    }
    
    return '<script type="application/ld+json">' . 
           json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . 
           '</script>';
}


/**
 * Generate HowTo Schema
 * 
 * @param string $name Title of the how-to
 * @param string $description Brief description
 * @param string $total_time ISO 8601 duration (e.g., 'PT10M' for 10 minutes)
 * @param array $steps Array of associative arrays with 'name', 'text', and 'url' keys
 * @param array $supplies Optional array of supply items (strings)
 * @param array $tools Optional array of tool items (strings)
 * @return string JSON-LD script tag
 * 
 * Example:
 *   $steps = [
 *       ['name' => 'Step 1', 'text' => 'Do this first', 'url' => 'https://site.com/guide#step1'],
 *       ['name' => 'Step 2', 'text' => 'Then this', 'url' => 'https://site.com/guide#step2']
 *   ];
 *   echo generate_howto_schema('How to Deploy', 'Deploy your app...', 'PT10M', $steps);
 */
function generate_howto_schema($name, $description, $total_time, $steps, $supplies = [], $tools = []) {
    $schema = [
        "@context" => "https://schema.org",
        "@type" => "HowTo",
        "name" => $name,
        "description" => $description,
        "totalTime" => $total_time,
        "step" => []
    ];
    
    // Add supplies if provided
    if (!empty($supplies)) {
        $schema["supply"] = [];
        foreach ($supplies as $supply) {
            $schema["supply"][] = [
                "@type" => "HowToSupply",
                "name" => $supply
            ];
        }
    }
    
    // Add tools if provided
    if (!empty($tools)) {
        $schema["tool"] = [];
        foreach ($tools as $tool) {
            $schema["tool"][] = [
                "@type" => "HowToTool",
                "name" => $tool
            ];
        }
    }
    
    // Add steps
    $position = 1;
    foreach ($steps as $step) {
        if (!isset($step['name']) || !isset($step['text'])) {
            continue; // Skip invalid entries
        }
        
        $step_data = [
            "@type" => "HowToStep",
            "position" => $position++,
            "name" => $step['name'],
            "text" => $step['text']
        ];
        
        if (isset($step['url'])) {
            $step_data["url"] = $step['url'];
        }
        
        $schema["step"][] = $step_data;
    }
    
    return '<script type="application/ld+json">' . 
           json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . 
           '</script>';
}


/**
 * Generate Article Schema
 * 
 * @param string $headline Article title
 * @param string $description Brief description
 * @param string $author_name Author's name
 * @param string $date_published Date in YYYY-MM-DD format
 * @param string $date_modified Optional modified date in YYYY-MM-DD format
 * @param string $image_url URL to featured image
 * @param string $page_url URL of the article page
 * @param string $publisher_name Name of publishing organization
 * @param string $publisher_logo_url URL to publisher logo
 * @return string JSON-LD script tag
 * 
 * Example:
 *   echo generate_article_schema(
 *       'My Article Title',
 *       'This article explains...',
 *       'Darin Hardin',
 *       '2025-01-15',
 *       '2025-03-20',
 *       'https://site.com/image.jpg',
 *       'https://site.com/article',
 *       'My Site',
 *       'https://site.com/logo.png'
 *   );
 */
function generate_article_schema($headline, $description, $author_name, $date_published, 
                                  $date_modified = null, $image_url = null, $page_url = null, 
                                  $publisher_name = 'Your Site', $publisher_logo_url = null) {
    $schema = [
        "@context" => "https://schema.org",
        "@type" => "Article",
        "headline" => $headline,
        "description" => $description,
        "author" => [
            "@type" => "Person",
            "name" => $author_name
        ],
        "publisher" => [
            "@type" => "Organization",
            "name" => $publisher_name
        ],
        "datePublished" => $date_published
    ];
    
    // Add optional fields
    if ($date_modified) {
        $schema["dateModified"] = $date_modified;
    } else {
        $schema["dateModified"] = $date_published;
    }
    
    if ($image_url) {
        $schema["image"] = $image_url;
    }
    
    if ($page_url) {
        $schema["mainEntityOfPage"] = [
            "@type" => "WebPage",
            "@id" => $page_url
        ];
    }
    
    if ($publisher_logo_url) {
        $schema["publisher"]["logo"] = [
            "@type" => "ImageObject",
            "url" => $publisher_logo_url
        ];
    }
    
    return '<script type="application/ld+json">' . 
           json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . 
           '</script>';
}


/**
 * Generate Product Schema (SoftwareApplication)
 * 
 * @param string $name Product/app name
 * @param string $description Brief description
 * @param string $category Application category (e.g., 'DeveloperApplication')
 * @param float $price Price (use 0 for free)
 * @param string $currency Currency code (default: USD)
 * @param string $screenshot_url URL to product screenshot
 * @param array $features Array of feature descriptions (strings)
 * @param string $operating_system Operating system(s) (e.g., 'Web, macOS, Windows')
 * @return string JSON-LD script tag
 * 
 * Example:
 *   $features = ['Feature 1', 'Feature 2', 'Feature 3'];
 *   echo generate_product_schema(
 *       'TaskFlow',
 *       'Simple task management tool',
 *       'ProductivityApplication',
 *       29.00,
 *       'USD',
 *       'https://site.com/screenshot.png',
 *       $features,
 *       'Web'
 *   );
 */
function generate_product_schema($name, $description, $category, $price, $currency = 'USD', 
                                  $screenshot_url = null, $features = [], $operating_system = 'Web') {
    $schema = [
        "@context" => "https://schema.org",
        "@type" => "SoftwareApplication",
        "name" => $name,
        "description" => $description,
        "applicationCategory" => $category,
        "operatingSystem" => $operating_system,
        "offers" => [
            "@type" => "Offer",
            "price" => number_format($price, 2, '.', ''),
            "priceCurrency" => $currency,
            "availability" => "https://schema.org/InStock"
        ]
    ];
    
    // Add optional fields
    if ($screenshot_url) {
        $schema["screenshot"] = $screenshot_url;
    }
    
    if (!empty($features)) {
        $schema["featureList"] = $features;
    }
    
    return '<script type="application/ld+json">' . 
           json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . 
           '</script>';
}


/**
 * Generate Organization Schema
 * 
 * @param string $name Organization name
 * @param string $url Website URL
 * @param string $logo_url Logo URL
 * @param string $description Brief description
 * @param array $social_urls Array of social media profile URLs
 * @param string $contact_email Optional contact email
 * @param string $contact_phone Optional contact phone
 * @return string JSON-LD script tag
 * 
 * Example:
 *   $social = ['https://twitter.com/mycompany', 'https://github.com/mycompany'];
 *   echo generate_organization_schema(
 *       'My Company',
 *       'https://mycompany.com',
 *       'https://mycompany.com/logo.png',
 *       'We build awesome tools',
 *       $social,
 *       'hello@mycompany.com'
 *   );
 */
function generate_organization_schema($name, $url, $logo_url, $description, 
                                       $social_urls = [], $contact_email = null, $contact_phone = null) {
    $schema = [
        "@context" => "https://schema.org",
        "@type" => "Organization",
        "name" => $name,
        "url" => $url,
        "logo" => $logo_url,
        "description" => $description
    ];
    
    // Add social media profiles
    if (!empty($social_urls)) {
        $schema["sameAs"] = $social_urls;
    }
    
    // Add contact information if provided
    if ($contact_email || $contact_phone) {
        $schema["contactPoint"] = [
            "@type" => "ContactPoint",
            "contactType" => "Customer Support"
        ];
        
        if ($contact_email) {
            $schema["contactPoint"]["email"] = $contact_email;
        }
        
        if ($contact_phone) {
            $schema["contactPoint"]["telephone"] = $contact_phone;
        }
    }
    
    return '<script type="application/ld+json">' . 
           json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . 
           '</script>';
}


/**
 * Generate Person Schema
 * 
 * @param string $name Person's name
 * @param string $url URL to person's website or about page
 * @param string $job_title Job title or role
 * @param string $description Brief bio
 * @param string $image_url Photo URL
 * @param array $social_urls Array of social profile URLs
 * @param array $knows_about Array of expertise areas (strings)
 * @return string JSON-LD script tag
 * 
 * Example:
 *   $social = ['https://twitter.com/username', 'https://github.com/username'];
 *   $expertise = ['Web Development', 'JavaScript', 'Python'];
 *   echo generate_person_schema(
 *       'Darin Hardin',
 *       'https://site.com/about',
 *       'Indie Developer',
 *       'I build tools for developers',
 *       'https://site.com/photo.jpg',
 *       $social,
 *       $expertise
 *   );
 */
function generate_person_schema($name, $url, $job_title, $description, 
                                 $image_url = null, $social_urls = [], $knows_about = []) {
    $schema = [
        "@context" => "https://schema.org",
        "@type" => "Person",
        "name" => $name,
        "url" => $url,
        "jobTitle" => $job_title,
        "description" => $description
    ];
    
    // Add optional fields
    if ($image_url) {
        $schema["image"] = $image_url;
    }
    
    if (!empty($social_urls)) {
        $schema["sameAs"] = $social_urls;
    }
    
    if (!empty($knows_about)) {
        $schema["knowsAbout"] = $knows_about;
    }
    
    return '<script type="application/ld+json">' . 
           json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . 
           '</script>';
}


/**
 * Combine Multiple Schemas
 * 
 * Use this to output multiple schema types on the same page.
 * 
 * @param array $schemas Array of schema strings (from other functions)
 * @return string Combined JSON-LD script tag
 * 
 * Example:
 *   $article = generate_article_schema(...);
 *   $faq = generate_faq_schema(...);
 *   echo combine_schemas([$article, $faq]);
 */
function combine_schemas($schemas) {
    // Extract JSON from each schema
    $json_objects = [];
    
    foreach ($schemas as $schema) {
        // Extract JSON from script tags
        preg_match('/<script[^>]*>(.*?)<\/script>/s', $schema, $matches);
        if (isset($matches[1])) {
            $json_objects[] = json_decode(trim($matches[1]), true);
        }
    }
    
    if (empty($json_objects)) {
        return '';
    }
    
    // Return combined array
    return '<script type="application/ld+json">' . 
           json_encode($json_objects, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . 
           '</script>';
}


/**
 * Helper: Get Current URL
 * 
 * Returns the current page URL (useful for schema generation)
 * 
 * @return string Current page URL
 */
function get_current_url() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['REQUEST_URI'];
    return $protocol . "://" . $host . $uri;
}


/**
 * Helper: Format Date for Schema
 * 
 * Converts various date formats to YYYY-MM-DD for schema
 * 
 * @param string|int $date Date string or Unix timestamp
 * @return string Date in YYYY-MM-DD format
 */
function format_schema_date($date) {
    if (is_numeric($date)) {
        // Unix timestamp
        return date('Y-m-d', $date);
    }
    
    // Parse date string
    $timestamp = strtotime($date);
    if ($timestamp === false) {
        // Return today if parsing fails
        return date('Y-m-d');
    }
    
    return date('Y-m-d', $timestamp);
}


/**
 * Helper: Format Duration for Schema (ISO 8601)
 * 
 * Converts minutes to ISO 8601 duration format
 * 
 * @param int $minutes Number of minutes
 * @return string Duration in ISO 8601 format (e.g., 'PT10M')
 */
function format_schema_duration($minutes) {
    if ($minutes < 60) {
        return "PT{$minutes}M";
    }
    
    $hours = floor($minutes / 60);
    $mins = $minutes % 60;
    
    if ($mins == 0) {
        return "PT{$hours}H";
    }
    
    return "PT{$hours}H{$mins}M";
}

?>

