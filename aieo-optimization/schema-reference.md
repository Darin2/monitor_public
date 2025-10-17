# Schema Markup Reference for AIEO

Complete JSON-LD schema examples for AI Engine Optimization. All examples are production-ready and tested.

## Table of Contents

1. [FAQ Schema](#faq-schema)
2. [HowTo Schema](#howto-schema)
3. [Article Schema](#article-schema)
4. [Product Schema](#product-schema)
5. [Organization Schema](#organization-schema)
6. [Person Schema](#person-schema)
7. [Combining Multiple Schemas](#combining-multiple-schemas)
8. [Common Mistakes](#common-mistakes)
9. [PHP Generators for Bluehost](#php-generators-for-bluehost)

---

## FAQ Schema

**When to use:** Any page with a Q&A section, pricing FAQs, product questions, troubleshooting guides.

**Why it matters:** AI engines heavily favor FAQ schema for direct answers to user queries.

### JSON-LD Example

```json
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "What is AIEO?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "AIEO (AI Engine Optimization) is the practice of structuring web content to maximize visibility in AI-powered search engines like ChatGPT, Perplexity, and Claude."
      }
    },
    {
      "@type": "Question",
      "name": "How is AIEO different from SEO?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "While SEO focuses on ranking in traditional search engines through backlinks and keywords, AIEO optimizes content structure and markup so AI models can easily parse, understand, and cite your content."
      }
    },
    {
      "@type": "Question",
      "name": "Do I need technical skills to implement AIEO?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Basic AIEO requires only content restructuring (lists, tables, clear headings). Advanced AIEO involves adding JSON-LD schema markup, which requires basic HTML editing skills."
      }
    }
  ]
}
</script>
```

### Best Practices

- ✅ Keep questions concise and natural (how people actually ask)
- ✅ Keep answers under 300 characters when possible (quotable length)
- ✅ Use complete sentences in answers (they should stand alone)
- ✅ Include 3-10 questions per page
- ❌ Don't stuff keywords unnaturally
- ❌ Don't use FAQ schema for non-question content

---

## HowTo Schema

**When to use:** Tutorials, guides, step-by-step instructions, recipes, installation guides.

**Why it matters:** AI engines use HowTo schema to provide step-by-step instructions in responses.

### JSON-LD Example

```json
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "HowTo",
  "name": "How to Deploy a Next.js App to Vercel",
  "description": "Step-by-step guide to deploying your Next.js application to Vercel hosting in under 5 minutes.",
  "totalTime": "PT5M",
  "estimatedCost": {
    "@type": "MonetaryAmount",
    "currency": "USD",
    "value": "0"
  },
  "supply": [
    {
      "@type": "HowToSupply",
      "name": "Next.js application"
    },
    {
      "@type": "HowToSupply",
      "name": "GitHub account"
    },
    {
      "@type": "HowToSupply",
      "name": "Vercel account"
    }
  ],
  "tool": [
    {
      "@type": "HowToTool",
      "name": "Git command line"
    }
  ],
  "step": [
    {
      "@type": "HowToStep",
      "position": 1,
      "name": "Push code to GitHub",
      "text": "Commit your Next.js project to a GitHub repository.",
      "url": "https://yoursite.com/deploy-nextjs#step-1"
    },
    {
      "@type": "HowToStep",
      "position": 2,
      "name": "Connect Vercel to GitHub",
      "text": "Sign in to Vercel and click 'Import Project', then select your GitHub repository.",
      "url": "https://yoursite.com/deploy-nextjs#step-2"
    },
    {
      "@type": "HowToStep",
      "position": 3,
      "name": "Configure and deploy",
      "text": "Vercel will auto-detect Next.js settings. Click 'Deploy' to build and publish your app.",
      "url": "https://yoursite.com/deploy-nextjs#step-3"
    }
  ]
}
</script>
```

### Best Practices

- ✅ Use ISO 8601 duration format for time (PT5M = 5 minutes, PT1H30M = 1.5 hours)
- ✅ Include supply/tool items for completeness
- ✅ Link each step to an anchor on the page
- ✅ Keep step text concise but complete
- ❌ Don't skip steps to make it look faster
- ❌ Don't number steps in the name field (position handles that)

---

## Article Schema

**When to use:** Blog posts, news articles, guides, case studies, long-form content.

**Why it matters:** Helps AI engines attribute authorship and understand publication context.

### JSON-LD Example

```json
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Article",
  "headline": "The Complete Guide to AIEO: Optimizing Content for AI Search Engines",
  "description": "Learn how to structure your web content for maximum visibility in ChatGPT, Perplexity, Claude, and other AI-powered search engines.",
  "image": "https://yoursite.com/images/aieo-guide-cover.jpg",
  "author": {
    "@type": "Person",
    "name": "Darin Hardin",
    "url": "https://yoursite.com/about"
  },
  "publisher": {
    "@type": "Organization",
    "name": "Your Site Name",
    "logo": {
      "@type": "ImageObject",
      "url": "https://yoursite.com/logo.png"
    }
  },
  "datePublished": "2025-01-15",
  "dateModified": "2025-03-20",
  "mainEntityOfPage": {
    "@type": "WebPage",
    "@id": "https://yoursite.com/aieo-guide"
  }
}
</script>
```

### Best Practices

- ✅ Use YYYY-MM-DD format for dates
- ✅ Update dateModified when you update content
- ✅ Include high-quality featured image (1200x630px recommended)
- ✅ Keep headline under 110 characters
- ✅ Write compelling description (150-160 characters)
- ❌ Don't use clickbait headlines
- ❌ Don't forget to update dateModified

---

## Product Schema

**When to use:** SaaS tools, products, plugins, services, downloadable software.

**Why it matters:** AI engines use Product schema to recommend tools and compare features.

### JSON-LD Example

```json
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "SoftwareApplication",
  "name": "Your Tool Name",
  "applicationCategory": "DeveloperApplication",
  "operatingSystem": "Web, macOS, Windows",
  "description": "Brief, clear description of what your tool does and who it's for.",
  "offers": {
    "@type": "Offer",
    "price": "29.00",
    "priceCurrency": "USD",
    "priceValidUntil": "2025-12-31",
    "availability": "https://schema.org/InStock"
  },
  "aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": "4.8",
    "ratingCount": "127"
  },
  "author": {
    "@type": "Person",
    "name": "Darin Hardin"
  },
  "screenshot": "https://yoursite.com/screenshot.png",
  "featureList": [
    "Feature 1 description",
    "Feature 2 description",
    "Feature 3 description"
  ]
}
</script>
```

### For Physical Products

```json
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Product",
  "name": "Product Name",
  "description": "Clear product description",
  "image": "https://yoursite.com/product-image.jpg",
  "brand": {
    "@type": "Brand",
    "name": "Your Brand"
  },
  "offers": {
    "@type": "Offer",
    "url": "https://yoursite.com/product",
    "priceCurrency": "USD",
    "price": "49.99",
    "availability": "https://schema.org/InStock",
    "seller": {
      "@type": "Organization",
      "name": "Your Company"
    }
  }
}
</script>
```

### Best Practices

- ✅ Use SoftwareApplication for digital tools
- ✅ Use Product for physical items
- ✅ Include real ratings only (don't fake)
- ✅ Update price and availability regularly
- ✅ List key features in featureList
- ❌ Don't inflate ratings
- ❌ Don't forget to update priceValidUntil

---

## Organization Schema

**When to use:** About pages, company pages, site-wide footer markup.

**Why it matters:** Establishes authority and trust signals for AI engines.

### JSON-LD Example

```json
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "Your Company Name",
  "url": "https://yoursite.com",
  "logo": "https://yoursite.com/logo.png",
  "description": "Brief description of what your company does",
  "contactPoint": {
    "@type": "ContactPoint",
    "telephone": "+1-555-123-4567",
    "contactType": "Customer Support",
    "email": "support@yoursite.com",
    "areaServed": "US",
    "availableLanguage": "English"
  },
  "sameAs": [
    "https://twitter.com/yourcompany",
    "https://linkedin.com/company/yourcompany",
    "https://github.com/yourcompany"
  ],
  "founder": {
    "@type": "Person",
    "name": "Darin Hardin"
  },
  "foundingDate": "2024"
}
</script>
```

### Best Practices

- ✅ Include social media profiles in sameAs
- ✅ Use consistent company name everywhere
- ✅ Add this to your site footer (site-wide)
- ✅ Include real contact information
- ❌ Don't list inactive social profiles
- ❌ Don't omit foundingDate (shows legitimacy)

---

## Person Schema

**When to use:** Author pages, about pages, personal blogs.

**Why it matters:** Builds personal authority and attribution for AI citations.

### JSON-LD Example

```json
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Person",
  "name": "Darin Hardin",
  "url": "https://yoursite.com/about",
  "image": "https://yoursite.com/photo.jpg",
  "jobTitle": "Indie Developer",
  "description": "Brief bio highlighting expertise and credibility",
  "sameAs": [
    "https://twitter.com/yourhandle",
    "https://github.com/yourhandle",
    "https://linkedin.com/in/yourprofile"
  ],
  "knowsAbout": [
    "Web Development",
    "AIEO",
    "JavaScript",
    "Python"
  ]
}
</script>
```

### Best Practices

- ✅ Use real photo (builds trust)
- ✅ List actual expertise areas
- ✅ Link to active social profiles
- ✅ Keep description under 200 characters
- ❌ Don't exaggerate credentials
- ❌ Don't use generic stock photos

---

## Combining Multiple Schemas

You can include multiple schema types on the same page. Use an array:

```json
<script type="application/ld+json">
[
  {
    "@context": "https://schema.org",
    "@type": "Article",
    "headline": "How to Build a Next.js App",
    "author": {
      "@type": "Person",
      "name": "Darin Hardin"
    },
    "datePublished": "2025-01-15"
  },
  {
    "@context": "https://schema.org",
    "@type": "HowTo",
    "name": "Build a Next.js App",
    "step": [
      {
        "@type": "HowToStep",
        "position": 1,
        "name": "Install Next.js",
        "text": "Run: npx create-next-app@latest"
      }
    ]
  },
  {
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
      {
        "@type": "Question",
        "name": "What is Next.js?",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Next.js is a React framework for building full-stack web applications."
        }
      }
    ]
  }
]
</script>
```

### When to Combine

- ✅ Article + HowTo (tutorial blog post)
- ✅ Article + FAQ (blog post with Q&A section)
- ✅ Product + FAQ (product page with questions)
- ✅ HowTo + FAQ (guide with troubleshooting)
- ❌ Don't add schema types that don't match your content

---

## Common Mistakes

### ❌ Mistake 1: Hidden Content
**Problem:** Adding FAQ schema for questions that don't appear on the page.  
**Fix:** Only mark up content that's visible to users.

### ❌ Mistake 2: Keyword Stuffing
**Problem:** "Best cheap affordable budget low-cost tool for..."  
**Fix:** Write naturally. AI engines detect and penalize keyword stuffing.

### ❌ Mistake 3: Duplicate Schema
**Problem:** Multiple Article schemas on the same page.  
**Fix:** Use one schema per type per page (unless it's a list/collection).

### ❌ Mistake 4: Broken URLs
**Problem:** Schema references URLs that 404.  
**Fix:** Validate all URLs before publishing.

### ❌ Mistake 5: Wrong Date Format
**Problem:** "January 15, 2025" or "01/15/2025"  
**Fix:** Always use YYYY-MM-DD format.

### ❌ Mistake 6: Fake Reviews
**Problem:** Adding aggregateRating without real reviews.  
**Fix:** Only include ratings if you have genuine user reviews.

### ❌ Mistake 7: Nested JSON Errors
**Problem:** Missing commas, extra commas, unmatched braces.  
**Fix:** Validate with https://validator.schema.org/

---

## PHP Generators for Bluehost

These PHP functions work on standard Bluehost shared hosting. Drop them into your PHP pages.

### FAQ Schema Generator

```php
<?php
function generate_faq_schema($faqs) {
    $schema = [
        "@context" => "https://schema.org",
        "@type" => "FAQPage",
        "mainEntity" => []
    ];
    
    foreach ($faqs as $faq) {
        $schema["mainEntity"][] = [
            "@type" => "Question",
            "name" => $faq["question"],
            "acceptedAnswer" => [
                "@type" => "Answer",
                "text" => $faq["answer"]
            ]
        ];
    }
    
    return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>';
}

// Usage:
$faqs = [
    ["question" => "What is AIEO?", "answer" => "AIEO is AI Engine Optimization..."],
    ["question" => "How does it work?", "answer" => "It works by structuring content..."]
];

echo generate_faq_schema($faqs);
?>
```

### Article Schema Generator

```php
<?php
function generate_article_schema($title, $description, $author, $date_published, $date_modified, $image_url, $page_url) {
    $schema = [
        "@context" => "https://schema.org",
        "@type" => "Article",
        "headline" => $title,
        "description" => $description,
        "image" => $image_url,
        "author" => [
            "@type" => "Person",
            "name" => $author
        ],
        "publisher" => [
            "@type" => "Organization",
            "name" => "Your Site Name",
            "logo" => [
                "@type" => "ImageObject",
                "url" => "https://yoursite.com/logo.png"
            ]
        ],
        "datePublished" => $date_published,
        "dateModified" => $date_modified,
        "mainEntityOfPage" => [
            "@type" => "WebPage",
            "@id" => $page_url
        ]
    ];
    
    return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>';
}

// Usage:
echo generate_article_schema(
    "My Article Title",
    "Article description",
    "Darin Hardin",
    "2025-01-15",
    "2025-03-20",
    "https://yoursite.com/image.jpg",
    "https://yoursite.com/article"
);
?>
```

### HowTo Schema Generator

```php
<?php
function generate_howto_schema($name, $description, $total_time, $steps) {
    $schema = [
        "@context" => "https://schema.org",
        "@type" => "HowTo",
        "name" => $name,
        "description" => $description,
        "totalTime" => $total_time,
        "step" => []
    ];
    
    $position = 1;
    foreach ($steps as $step) {
        $schema["step"][] = [
            "@type" => "HowToStep",
            "position" => $position++,
            "name" => $step["name"],
            "text" => $step["text"],
            "url" => $step["url"]
        ];
    }
    
    return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>';
}

// Usage:
$steps = [
    ["name" => "Step 1", "text" => "Do this first", "url" => "https://yoursite.com/guide#step1"],
    ["name" => "Step 2", "text" => "Then do this", "url" => "https://yoursite.com/guide#step2"]
];

echo generate_howto_schema(
    "How to Deploy App",
    "Guide to deploying your app",
    "PT10M",
    $steps
);
?>
```

### Product Schema Generator

```php
<?php
function generate_product_schema($name, $description, $image, $price, $currency = "USD") {
    $schema = [
        "@context" => "https://schema.org",
        "@type" => "SoftwareApplication",
        "name" => $name,
        "description" => $description,
        "applicationCategory" => "DeveloperApplication",
        "operatingSystem" => "Web",
        "screenshot" => $image,
        "offers" => [
            "@type" => "Offer",
            "price" => $price,
            "priceCurrency" => $currency,
            "availability" => "https://schema.org/InStock"
        ]
    ];
    
    return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>';
}

// Usage:
echo generate_product_schema(
    "My Awesome Tool",
    "A tool that helps developers...",
    "https://yoursite.com/screenshot.png",
    "29.00"
);
?>
```

---

## Validation

Always validate your schema before publishing:

1. **Schema.org Validator:** https://validator.schema.org/
2. **Google Rich Results Test:** https://search.google.com/test/rich-results
3. **JSON Validator:** https://jsonlint.com/

Paste your schema JSON (without the `<script>` tags) into these tools.

---

## Quick Reference

| Content Type | Schema Type | Priority |
|--------------|-------------|----------|
| FAQ section | FAQPage | ⭐⭐⭐ High |
| Tutorial | HowTo | ⭐⭐⭐ High |
| Blog post | Article | ⭐⭐ Medium |
| Product page | SoftwareApplication/Product | ⭐⭐⭐ High |
| About page | Person/Organization | ⭐⭐ Medium |
| Review | Review | ⭐⭐ Medium |

**Start with high-priority schemas** for maximum AIEO impact.

