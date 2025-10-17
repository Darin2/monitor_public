---
name: "AIEO Optimization"
description: "Optimize content for AI search engines (ChatGPT, Perplexity, Claude, Google AI Overviews). Use when creating web content, landing pages, blog posts, tutorials, or any public-facing content that should rank in AI search results. Includes schema markup patterns, content templates, and validation tools."
version: "0.1.0"
author: "Darin Hardin"
tags: ["seo", "aieo", "content-optimization", "schema", "ai-search"]
---

# AIEO Optimization Skill

## What is AIEO?

**AIEO (AI Engine Optimization)** is the practice of structuring web content to maximize visibility and citation in AI-powered search engines like ChatGPT, Perplexity, Claude, Google AI Overviews, and Bing Copilot.

Unlike traditional SEO (which optimizes for Google's link-based ranking), AIEO focuses on:
- Making content **easily parseable** by AI models
- Providing **structured, quotable information**
- Using **semantic markup** that AI systems understand
- Creating **definitive, authoritative content** that AI trusts to cite

## Why AIEO Matters

AI search engines are rapidly changing how people find information:
- **ChatGPT Search** launched October 2024
- **Perplexity** is growing 15% month-over-month
- **Google AI Overviews** appear in 84% of searches
- **Claude** and other AI assistants browse and cite web content

If your content isn't optimized for AI engines, you're invisible to millions of searches.

## When to Use This Skill

Load this skill when you're:
- ✅ Creating a new landing page, product page, or tool page
- ✅ Writing blog posts, tutorials, or guides
- ✅ Optimizing existing content for AI visibility
- ✅ Adding schema markup to web pages
- ✅ Reviewing content for AI-friendliness
- ✅ Building comparison pages or reviews

## Core AIEO Principles

### 1. Definition-First Content
Start with a clear, concise definition. No fluff before substance.

**Bad:** "In today's fast-paced digital world, many people are wondering..."  
**Good:** "X is a Y that does Z. It helps [target audience] achieve [outcome]."

### 2. Structured Formats Over Prose
AI engines parse structured content more reliably:
- **Use lists** instead of long paragraphs
- **Use tables** for comparisons, features, pricing
- **Use Q&A format** for FAQs
- **Use step-by-step instructions** for tutorials
- **Use headings** to create clear hierarchy

### 3. Quotable, Factual Statements
Write sentences that can stand alone as complete thoughts:
- Short, declarative sentences
- Specific numbers and data points
- Clear cause-and-effect relationships
- Explicit comparisons ("Unlike X, Y does...")

### 4. Proper Schema Markup
Add JSON-LD structured data to help AI understand your content:
- **FAQ schema** for Q&A sections
- **HowTo schema** for tutorials
- **Article schema** for blog posts
- **Product schema** for tools/services
- **Organization/Person schema** for authorship

### 5. Semantic HTML
Use HTML elements that convey meaning:
- `<article>`, `<section>`, `<aside>` over generic `<div>`
- Proper heading hierarchy (H1 → H2 → H3)
- `<time>` for dates
- `<address>` for contact info
- Lists (`<ul>`, `<ol>`) for enumerated content

### 6. Clear Authorship and Dates
AI engines trust timestamped, attributed content:
- Show publish date and last updated date
- Include author name and credentials
- Link to author bio or about page
- Use structured data for author info

### 7. Original Insights Over Rehashed Content
AI engines penalize generic content:
- Share unique data, experiences, or methods
- Include case studies or real examples
- Provide specific recommendations
- Avoid content that's just aggregated from other sources

## How to Apply AIEO (Instructions for Claude)

When I ask you to optimize content for AIEO:

1. **Analyze the content type** and choose the right template from `content-templates.md`
2. **Restructure content** to follow AIEO principles:
   - Lead with clear definition
   - Break paragraphs into lists
   - Add comparison tables where useful
   - Create clear heading hierarchy
3. **Add appropriate schema markup** using examples from `schema-reference.md`
4. **Ensure semantic HTML** structure
5. **Add/improve authorship and date metadata**
6. **Run through validation checklist** from `validation.md`

## File Reference Guide

This skill is organized across multiple files for easy maintenance:

### 📘 [schema-reference.md](schema-reference.md)
Complete JSON-LD schema examples with:
- FAQ, HowTo, Article, Product, Organization, Person schemas
- When to use each type
- How to combine multiple schemas
- Common mistakes to avoid
- PHP snippets for Bluehost hosting

### 📗 [content-templates.md](content-templates.md)
Ready-to-use templates for:
- Product/tool pages
- Tutorial/guide pages
- Comparison/review pages
- Blog posts
- Landing pages

Each template includes structure guidelines, example content, schema markup locations, and checklists.

### 📙 [validation.md](validation.md)
Quality assurance tools:
- Pre-publish AIEO checklist
- AI search engine testing procedures
- Schema validation process
- A/B testing framework

### 🔧 [scripts/validate_aieo.py](scripts/validate_aieo.py)
Python validation script that checks:
- JSON-LD schema presence and validity
- Heading hierarchy
- Structured content (lists, tables)
- Author and date information
- Link text quality

**Usage:** `python validate_aieo.py <url or file>`

### 🔧 [scripts/generate_schema.php](scripts/generate_schema.php)
PHP helper functions for Bluehost:
- FAQ schema generator
- HowTo schema generator
- Article schema generator
- Product schema generator

Easy to drop into existing PHP pages.

## Quick Start Examples

### Example 1: Optimize a Product Page

**User asks:** "Optimize this landing page for AIEO"

**You should:**
1. Restructure using the Product Page template from `content-templates.md`
2. Add Product schema markup from `schema-reference.md`
3. Convert feature paragraphs to bulleted lists
4. Add comparison table if competitors mentioned
5. Ensure H1 starts with clear definition
6. Add FAQ section with FAQ schema
7. Run through validation checklist

### Example 2: Create a Tutorial

**User asks:** "Write an AI-friendly tutorial on how to deploy a Next.js app"

**You should:**
1. Use Tutorial template from `content-templates.md`
2. Structure as numbered steps
3. Add HowTo schema from `schema-reference.md`
4. Include time estimates for each step
5. Add tools/materials section
6. Use clear, imperative language ("Click X", "Run Y")
7. Include troubleshooting section

### Example 3: Add Schema Markup

**User asks:** "Add JSON-LD schema to my blog post"

**You should:**
1. Choose Article schema from `schema-reference.md`
2. Include author, publish date, headline, image
3. If there's a FAQ section, add FAQ schema too
4. Show where to place the schema in the HTML
5. Provide PHP version if they're on Bluehost

## Tips for Indie Builders

This skill is designed for **practical, copy-paste solutions**:
- All templates have placeholders like `[Your Tool Name]` - easy to find and replace
- PHP scripts work on Bluehost shared hosting (no special setup)
- Python validation script has minimal dependencies
- Schema examples are complete and tested
- No build tools or complex workflows required

## Updating This Skill

AIEO is an emerging field. Update this skill as you learn:
- Add new schema types to `schema-reference.md`
- Refine templates in `content-templates.md` based on results
- Update validation rules in `validation.md`
- Track what works in real AI citations

## Success Metrics

You'll know AIEO is working when:
- ✅ AI engines cite your content directly
- ✅ Your pages appear in Perplexity sources
- ✅ ChatGPT references your tool in recommendations
- ✅ Claude cites your tutorial in responses
- ✅ Google AI Overviews quote your content
- ✅ Schema validates in Google Rich Results Test

## Additional Resources

- **Test Your Schema:** https://validator.schema.org/
- **Google Rich Results Test:** https://search.google.com/test/rich-results
- **Perplexity:** Search for your content to see if it appears
- **ChatGPT Search:** Test queries that should surface your content

---

**Next Steps:** When you're ready to optimize content, tell me what you're working on and I'll apply the appropriate AIEO patterns from this skill.

