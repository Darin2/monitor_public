# AIEO Optimization - Claude Skill

A comprehensive Claude Skill for optimizing web content for AI search engines (ChatGPT, Perplexity, Claude, Google AI Overviews).

## What's Inside

This skill provides everything you need to make your content discoverable and citable by AI engines:

### 📘 Core Documentation

- **[SKILL.md](SKILL.md)** - Main skill file with AIEO principles and usage instructions
- **[schema-reference.md](schema-reference.md)** - Complete JSON-LD schema examples and PHP generators
- **[content-templates.md](content-templates.md)** - Ready-to-use templates for common content types
- **[validation.md](validation.md)** - Checklists, testing procedures, and A/B testing framework

### 🔧 Tools

- **[scripts/validate_aieo.py](scripts/validate_aieo.py)** - Python validation script for AIEO compliance
- **[scripts/generate_schema.php](scripts/generate_schema.php)** - PHP functions for Bluehost hosting
- **[scripts/requirements.txt](scripts/requirements.txt)** - Python dependencies

## Quick Start

### For Claude Users

1. **Load the skill** in Claude by referencing the `aieo-optimization` directory
2. **Ask Claude to optimize content:**
   - "Optimize this landing page for AIEO"
   - "Create an AI-friendly tutorial on [topic]"
   - "Add schema markup to my blog post"
3. **Claude will automatically:**
   - Apply AIEO principles
   - Generate schema markup
   - Restructure content for AI engines
   - Provide validation checklist

### For Manual Use

If you're applying AIEO principles manually:

1. **Read [SKILL.md](SKILL.md)** for core principles
2. **Choose a template** from [content-templates.md](content-templates.md)
3. **Add schema markup** using examples from [schema-reference.md](schema-reference.md)
4. **Validate your content** with [validation.md](validation.md) checklist
5. **Run the validator:**
   ```bash
   cd scripts
   pip install -r requirements.txt
   python validate_aieo.py https://yoursite.com/page
   ```

## Installation

### Python Validation Script

```bash
cd aieo-optimization/scripts
pip install -r requirements.txt
python validate_aieo.py <url or file>
```

**Examples:**
```bash
# Validate a live URL
python validate_aieo.py https://yoursite.com/blog-post

# Validate a local HTML file
python validate_aieo.py ../my-page.html

# Save report to file
python validate_aieo.py https://yoursite.com/page > report.txt
```

### PHP Schema Generators (Bluehost)

1. Upload `scripts/generate_schema.php` to your web server
2. Include in your PHP page:
   ```php
   <?php require_once 'generate_schema.php'; ?>
   ```
3. Generate schema:
   ```php
   $faqs = [
       ['question' => 'What is AIEO?', 'answer' => 'AIEO is...'],
       ['question' => 'How does it work?', 'answer' => 'It works by...']
   ];
   echo generate_faq_schema($faqs);
   ```

See [schema-reference.md](schema-reference.md) for complete PHP examples.

## AIEO Principles at a Glance

1. **Definition-first content** - Clear, concise opening (no fluff)
2. **Structured formats** - Lists, tables, Q&A over long paragraphs
3. **Quotable statements** - Short, factual, complete sentences
4. **Proper schema markup** - JSON-LD for FAQ, HowTo, Article, Product
5. **Semantic HTML** - Use `<article>`, `<time>`, proper headings
6. **Clear authorship** - Show author and publish date
7. **Original insights** - Unique value over rehashed content

## Content Templates

Ready-to-use templates in [content-templates.md](content-templates.md):

- ✅ **Product/Tool Page** - SaaS, apps, plugins
- ✅ **Tutorial/Guide** - Step-by-step instructions
- ✅ **Comparison/Review** - "X vs. Y" posts
- ✅ **Blog Post** - Articles, case studies
- ✅ **Landing Page** - Product launches, lead gen

Each template includes:
- Structure guidelines
- Example HTML with schema
- Content checklist
- AIEO best practices

## Schema Types Supported

Complete examples and PHP generators in [schema-reference.md](schema-reference.md):

- **FAQPage** - Q&A sections (high AIEO value)
- **HowTo** - Tutorials and guides (high AIEO value)
- **Article** - Blog posts, news articles
- **Product/SoftwareApplication** - Tools, apps, services
- **Organization** - Company info, about pages
- **Person** - Author pages, personal sites

## Validation Checklist

Before publishing, check:

- [ ] Single H1 with clear topic
- [ ] Opening defines what content is (< 3 sentences)
- [ ] Proper heading hierarchy (H1 → H2 → H3)
- [ ] Content uses lists and tables (not walls of text)
- [ ] Appropriate schema markup added
- [ ] Schema validates at https://validator.schema.org/
- [ ] Author and publish date shown
- [ ] Images have descriptive alt text
- [ ] No "click here" or generic link text
- [ ] Meta description 150-160 characters

Run the full checklist: [validation.md](validation.md)

## Testing Your AIEO

After publishing, test in AI search engines:

**ChatGPT Search** (with Plus):
- Ask questions your content answers
- Check if ChatGPT cites your page

**Perplexity** (https://perplexity.ai):
- Search for your topics
- Check "Sources" section for your URL

**Google AI Overviews**:
- Search Google for target keywords
- See if AI Overview cites your content

**Track citations** to learn what works. See [validation.md](validation.md) for tracking templates.

## Real-World Examples

### Before AIEO:
```html
<h1>Welcome to Our Blog</h1>
<p>Hey everyone! Today I want to talk about something really cool...</p>
```
❌ Generic heading, no definition, conversational fluff

### After AIEO:
```html
<h1>AIEO (AI Engine Optimization): Optimizing Content for AI Search Engines</h1>
<p>AIEO is the practice of structuring web content to maximize visibility 
in AI-powered search engines like ChatGPT, Perplexity, and Claude.</p>

<h2>Key Principles</h2>
<ul>
  <li><strong>Definition-first content</strong> - Clear opening with no fluff</li>
  <li><strong>Structured formats</strong> - Lists and tables over paragraphs</li>
  <li><strong>Schema markup</strong> - JSON-LD for machine readability</li>
</ul>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Article",
  "headline": "AIEO: Optimizing Content for AI Search Engines",
  "author": {"@type": "Person", "name": "Darin Hardin"},
  "datePublished": "2025-01-15"
}
</script>
```
✅ Clear heading, definition-first, structured lists, schema markup

## Updating This Skill

AIEO is emerging. Update as you learn:

1. **Track what gets cited** by AI engines
2. **A/B test approaches** (see [validation.md](validation.md))
3. **Update templates** with winning patterns
4. **Add new schema types** to [schema-reference.md](schema-reference.md)
5. **Share insights** to improve the skill

## Resources

**Schema Validation:**
- https://validator.schema.org/ - Schema.org validator
- https://search.google.com/test/rich-results - Google Rich Results
- https://jsonlint.com/ - JSON syntax validator

**AI Search Engines:**
- https://perplexity.ai - Perplexity AI search
- https://chatgpt.com - ChatGPT (Plus has web search)
- https://claude.ai - Claude AI
- https://bing.com/chat - Bing Copilot

**Testing Tools:**
- https://hemingwayapp.com/ - Readability checker
- Web Vitals - Page speed and performance

## Contributing

Found something that works? Update the skill:

1. Test your approach (track citations)
2. Document what worked
3. Add examples to appropriate file
4. Update validation criteria if needed

## License

This skill is provided as-is for personal and commercial use. Modify freely for your needs.

---

## Quick Command Reference

```bash
# Validate a URL
python scripts/validate_aieo.py https://yoursite.com/page

# Validate local file
python scripts/validate_aieo.py path/to/file.html

# Install Python dependencies
pip install -r scripts/requirements.txt
```

```php
// Generate FAQ schema
<?php
require_once 'generate_schema.php';
$faqs = [['question' => 'Q1', 'answer' => 'A1']];
echo generate_faq_schema($faqs);
?>
```

---

**Need help?** Start with [SKILL.md](SKILL.md) for an overview, then dive into specific files for details.

**Ready to optimize?** Ask Claude to "optimize this content for AIEO" and let the skill guide you!

