# AIEO Validation Guide

Quality assurance checklist and testing procedures to ensure your content is optimized for AI search engines.

## Table of Contents

1. [Pre-Publish Checklist](#pre-publish-checklist)
2. [Schema Validation](#schema-validation)
3. [Content Structure Validation](#content-structure-validation)
4. [AI Search Engine Testing](#ai-search-engine-testing)
5. [Citation Tracking](#citation-tracking)
6. [A/B Testing Framework](#ab-testing-framework)
7. [Automated Validation](#automated-validation)

---

## Pre-Publish Checklist

Use this checklist before publishing any AIEO-optimized content.

### ✅ Content Structure

- [ ] **H1 exists and includes primary topic + benefit**
  - Example: "TaskFlow – Simple Task Management for Indie Developers"
  - Not: "Welcome" or "Home"

- [ ] **Opening paragraph defines what the content is (< 3 sentences)**
  - Should answer: What is this? Who is it for? Why does it matter?
  - No fluff before substance

- [ ] **Heading hierarchy is correct (H1 → H2 → H3, no skips)**
  - Only one H1 per page
  - H2s are main sections
  - H3s are subsections under H2s

- [ ] **Content uses structured formats:**
  - [ ] Bulleted lists for features, benefits, examples
  - [ ] Numbered lists for steps, processes, rankings
  - [ ] Tables for comparisons, pricing, specifications
  - [ ] Q&A format for FAQs

- [ ] **Paragraphs are short (2-4 sentences max)**
  - Each paragraph = one idea
  - No walls of text

- [ ] **Sentences are concise (< 25 words average)**
  - Remove filler words ("very", "really", "actually")
  - Use active voice ("AI engines parse content" not "Content is parsed by AI engines")

### ✅ Schema Markup

- [ ] **Appropriate schema type added:**
  - [ ] FAQ schema for Q&A sections
  - [ ] HowTo schema for tutorials
  - [ ] Article schema for blog posts
  - [ ] Product/SoftwareApplication schema for tools
  - [ ] Person/Organization schema for author/company pages

- [ ] **Schema is placed in `<head>` or end of `<body>`**
  - Wrapped in `<script type="application/ld+json">`

- [ ] **Schema validates without errors:**
  - Test at: https://validator.schema.org/
  - Test at: https://search.google.com/test/rich-results

- [ ] **Schema matches visible content:**
  - FAQ schema questions appear on page
  - HowTo steps match actual steps
  - Article schema date matches publish date

- [ ] **All required fields completed:**
  - Article: headline, author, datePublished, publisher
  - FAQ: at least 3 questions with answers
  - HowTo: name, description, at least 3 steps
  - Product: name, description, offers with price

### ✅ Metadata & Attribution

- [ ] **Author name visible and linked to bio/about page**

- [ ] **Publish date shown in human-readable format**
  - Example: "January 15, 2025"
  - Include `<time datetime="2025-01-15">` for machine readability

- [ ] **Last updated date shown (if content was updated)**
  - Shows content freshness
  - Update `dateModified` in Article schema

- [ ] **Meta description exists and is compelling (150-160 chars)**
  - Should include primary keyword
  - Should summarize key benefit

- [ ] **Page title (< 60 characters) includes primary keyword**

### ✅ Semantic HTML

- [ ] **Main content wrapped in `<article>` tag**

- [ ] **Dates use `<time datetime="YYYY-MM-DD">` tag**

- [ ] **Code snippets use `<pre><code>` tags**

- [ ] **Lists use `<ul>` or `<ol>`, not manual bullets**

- [ ] **Strong emphasis uses `<strong>`, not `<b>`**

- [ ] **Emphasis uses `<em>`, not `<i>`**

### ✅ Links & Navigation

- [ ] **All links use descriptive anchor text:**
  - Good: "Read our guide to AIEO"
  - Bad: "Click here" or "Learn more"

- [ ] **Internal links to related content included (3-5 minimum)**

- [ ] **External links to authoritative sources (if citing data)**

- [ ] **All links tested (no 404s)**

### ✅ Images & Media

- [ ] **All images have descriptive alt text:**
  - Good: "TaskFlow kanban board showing three columns: To Do, In Progress, Done"
  - Bad: "Screenshot" or "image1.png"

- [ ] **Featured image is high quality (min 1200x630px)**

- [ ] **Images are optimized (< 200KB per image)**

- [ ] **Image URLs included in schema markup**

### ✅ Quotability

- [ ] **Key points are complete standalone sentences:**
  - Should make sense out of context
  - AI engines often quote single sentences

- [ ] **Specific numbers and data points included:**
  - "1,200 users" not "many users"
  - "5 minutes" not "quick"

- [ ] **Explicit comparisons made where relevant:**
  - "Unlike Notion, Obsidian stores files locally"
  - "TaskFlow costs $12/mo compared to Jira's $7.75/mo"

- [ ] **Definitive statements (not hedging):**
  - Good: "AIEO is the practice of optimizing content for AI search engines"
  - Bad: "AIEO might be considered a way to potentially optimize..."

---

## Schema Validation

### Step 1: Extract Schema

Copy your JSON-LD schema (without the `<script>` tags) from your HTML.

### Step 2: Validate at Schema.org

1. Go to https://validator.schema.org/
2. Paste your schema JSON
3. Check for errors or warnings
4. Fix any issues

**Common errors:**
- Missing required fields (`@context`, `@type`)
- Wrong data types (string instead of number for price)
- Invalid date format (use YYYY-MM-DD)
- Broken URLs in schema

### Step 3: Test in Google Rich Results

1. Go to https://search.google.com/test/rich-results
2. Enter your page URL or HTML
3. Check if Google can read your schema
4. Look for "Valid items detected"

**Note:** Not all schema types show in Google (e.g., Person schema), but validation still matters for AI engines.

### Step 4: Validate JSON Syntax

If schema fails to parse:
1. Go to https://jsonlint.com/
2. Paste your JSON (without `<script>` tags)
3. Check for syntax errors (missing commas, unmatched braces)

### Schema Validation Checklist

- [ ] No errors in Schema.org validator
- [ ] No warnings in Schema.org validator (or warnings are intentional)
- [ ] Google Rich Results Test detects valid items
- [ ] JSON syntax is valid (no parse errors)
- [ ] All URLs in schema are absolute (https://...), not relative (/path)
- [ ] All required properties for schema type are present
- [ ] Schema matches visible page content

---

## Content Structure Validation

### Heading Hierarchy Test

**Tool:** Browser DevTools or `grep "<h[1-6]" yourfile.html`

**Check:**
- [ ] Exactly one `<h1>` per page
- [ ] H2s follow H1
- [ ] H3s follow H2s (no H3 directly under H1)
- [ ] Headings aren't skipped (no H1 → H3)
- [ ] Headings describe section content accurately

### List and Table Check

**What to verify:**
- [ ] Features presented as bulleted lists (not paragraphs)
- [ ] Steps presented as numbered lists
- [ ] Comparisons use tables (not prose)
- [ ] Pricing uses tables
- [ ] Tables have `<thead>` and `<tbody>` tags
- [ ] Table headers use `<th>`, not `<td>`

### Readability Metrics

**Use:** Hemingway Editor (https://hemingwayapp.com/) or similar

**Target metrics:**
- [ ] Grade level: 8-10 (college not required, but not childish)
- [ ] Sentences: 80%+ green or blue (avoid yellow/red complex sentences)
- [ ] Passive voice: < 10% of sentences
- [ ] Adverbs: Minimal (remove "very", "really", "quite")

**Note:** AI engines can parse complex text, but simpler = more quotable.

### Link Quality Check

**Manual review:**
- [ ] No "click here" or "here" as link text
- [ ] No generic "learn more" without context
- [ ] Each link describes destination
- [ ] 3-5 internal links to related content
- [ ] External links to high-authority sources (if citing stats)

### Image Alt Text Review

**Check each image:**
- [ ] Alt text describes what's in the image
- [ ] Alt text includes context (not just "screenshot")
- [ ] Alt text is < 125 characters
- [ ] No "image of" or "picture of" prefix (redundant)

---

## AI Search Engine Testing

After publishing, test how AI engines surface your content.

### ChatGPT Search Testing

**If you have ChatGPT Plus with search enabled:**

1. Ask questions your content answers:
   - "What is AIEO?"
   - "How do I deploy Next.js to Vercel?"
   - "TaskFlow vs Trello comparison"

2. Check if ChatGPT:
   - [ ] Cites your content
   - [ ] Quotes from your page
   - [ ] Links to your page
   - [ ] Pulls correct information

3. Note what it quotes (helps identify quotable content)

**What works:**
- Short, definitive statements
- Bulleted lists
- Table data
- FAQ answers

### Perplexity Testing

**Go to https://perplexity.ai and search:**

1. Enter queries related to your content
2. Check "Sources" section
3. Look for your URL

**Perplexity favors:**
- [ ] Content with clear structure
- [ ] FAQ schema (often quoted directly)
- [ ] Recent content (check dateModified)
- [ ] Quotable one-liners

**Track:**
- Query terms that surface your content
- What Perplexity quotes from your page
- How your content ranks in sources (1st? 3rd? Not shown?)

### Claude Testing (via API or Citation Check)

1. Ask Claude questions your content addresses
2. Use "search the web" mode if available
3. Check if Claude cites your content

**Note:** Claude favors authoritative, well-structured content with clear attribution.

### Google AI Overviews

1. Search Google for your target keywords
2. Check if AI Overview appears at top
3. See if your content is cited in the overview

**Check:**
- [ ] Does AI Overview appear for your keywords?
- [ ] Is your site cited in the overview?
- [ ] What snippet is pulled from your page?
- [ ] Are competitors cited instead?

### Bing Copilot Testing

1. Use Bing with Copilot enabled
2. Ask questions related to your content
3. Check if Bing cites your page

---

## Citation Tracking

Track when and how AI engines cite your content.

### Manual Tracking Spreadsheet

Create a simple spreadsheet to log citations:

| Date | AI Engine | Query | Cited? | Position | What Was Quoted | URL |
|------|-----------|-------|--------|----------|-----------------|-----|
| 2025-01-15 | Perplexity | "what is aieo" | Yes | 2nd source | FAQ answer definition | /aieo-guide |
| 2025-01-16 | ChatGPT | "deploy nextjs vercel" | Yes | N/A | Step 2 of tutorial | /deploy-nextjs |

**Track:**
- Which queries trigger citations
- Which AI engines cite you most
- What content gets quoted (headings, lists, FAQs)
- Position in sources (1st, 2nd, 3rd, not shown)

### Google Search Console

Monitor traditional search presence:
- Impressions and clicks over time
- Which queries drive traffic
- Click-through rate changes
- AI Overview appearances (if Google reports them)

### Analytics Review

Check your web analytics for:
- **Traffic sources:** Look for referrals from AI engines (may show as "perplexity.ai" or direct)
- **Top landing pages:** Which AIEO-optimized pages get most traffic?
- **Bounce rate:** Are AI-referred visitors engaging?
- **Time on page:** Do visitors from AI engines stay longer? (Indicator of quality)

**Note:** AI engine traffic may appear as "direct" or "other" in analytics, making it hard to track.

### Set Up Alerts

**Google Alerts:**
- Set alerts for "[your brand name] AIEO"
- Monitor mentions of your content
- See if others reference your AIEO-optimized pages

**Social Monitoring:**
- Search Twitter for your URL
- Check Reddit mentions
- See if people share your content in context of AI search

---

## A/B Testing Framework

Test what AIEO strategies work best for your content.

### What to Test

1. **Heading Styles:**
   - Test: "What is AIEO?" vs. "AIEO Definition" vs. "AIEO: AI Engine Optimization Explained"
   - Measure: Which gets cited more by AI engines?

2. **Content Structure:**
   - Test: Long paragraphs vs. bulleted lists
   - Measure: Which format gets quoted more?

3. **FAQ Positioning:**
   - Test: FAQ at top vs. FAQ at bottom
   - Measure: Citation rate difference

4. **Schema Types:**
   - Test: Article schema alone vs. Article + FAQ + HowTo
   - Measure: Rich result appearance

5. **Opening Styles:**
   - Test: Definition-first vs. story-first vs. problem-first
   - Measure: Which gets quoted as definition?

### A/B Testing Process

**Step 1: Create Two Versions**
- Version A: Current approach
- Version B: Alternative approach
- Keep everything else identical

**Step 2: Publish Both**
- Different pages (e.g., `/guide-a` and `/guide-b`)
- Or update one page, track before/after

**Step 3: Wait 2-4 Weeks**
- AI engines need time to index
- Check citations weekly

**Step 4: Compare Results**
- Which version gets cited more?
- Which version ranks higher in sources?
- Which version gets more traffic?

**Step 5: Implement Winner**
- Update all content with winning approach
- Document what worked

### Testing Log Template

| Test # | Hypothesis | Version A | Version B | Winner | Insight | Date |
|--------|------------|-----------|-----------|--------|---------|------|
| 1 | FAQ at top gets more citations | FAQ at bottom | FAQ at top | B | AI engines scan top of page first | 2025-01-15 |
| 2 | Lists beat paragraphs | Paragraph features | Bulleted features | B | Bulleted lists quoted 3x more | 2025-01-22 |

---

## Automated Validation

Use the included validation script to automate checks.

### Using validate_aieo.py

**Basic usage:**
```bash
python scripts/validate_aieo.py <url or file>
```

**Examples:**
```bash
# Validate a live URL
python scripts/validate_aieo.py https://yoursite.com/blog-post

# Validate a local HTML file
python scripts/validate_aieo.py path/to/article.html

# Save results to file
python scripts/validate_aieo.py https://yoursite.com/blog-post > validation-report.txt
```

**What it checks:**
- [ ] JSON-LD schema presence
- [ ] Schema syntax validity
- [ ] Heading hierarchy (H1 → H2 → H3)
- [ ] H1 existence and count
- [ ] Meta description presence and length
- [ ] Author information
- [ ] Publish date
- [ ] List and table presence
- [ ] Link text quality (no "click here")
- [ ] Image alt text presence

**Output example:**
```
AIEO Validation Report
======================
URL: https://yoursite.com/blog-post

✅ PASSED: JSON-LD schema found (Article, FAQPage)
✅ PASSED: Single H1 found: "How to Deploy Next.js to Vercel"
✅ PASSED: Heading hierarchy correct (H1 → H2 → H3)
✅ PASSED: Author information present
✅ PASSED: Publish date found
✅ PASSED: Lists found (5 bulleted lists, 1 numbered list)
✅ PASSED: Tables found (2 tables)
⚠️  WARNING: Meta description is 145 chars (ideal: 150-160)
❌ FAILED: Found 2 links with generic text ("click here", "learn more")
❌ FAILED: 3 images missing alt text

Overall Score: 8/11 (73%)

Recommendations:
- Update generic link text to be descriptive
- Add alt text to all images
- Lengthen meta description to 150-160 characters
```

### CI/CD Integration

**Add to GitHub Actions workflow:**

```yaml
name: AIEO Validation

on: [push, pull_request]

jobs:
  validate:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Set up Python
        uses: actions/setup-python@v2
        with:
          python-version: '3.9'
      - name: Install dependencies
        run: |
          pip install -r scripts/requirements.txt
      - name: Validate AIEO compliance
        run: |
          python scripts/validate_aieo.py public/index.html
```

**This will:**
- Run validation on every commit
- Block merges if validation fails
- Ensure AIEO standards are maintained

---

## Validation Workflow Summary

### Before Publishing

1. **Run pre-publish checklist** (manual review)
2. **Validate schema** at Schema.org and Google Rich Results
3. **Check content structure** (headings, lists, tables)
4. **Run automated validator** (`validate_aieo.py`)
5. **Fix all errors** and warnings
6. **Publish**

### After Publishing (Week 1)

7. **Test in AI search engines** (ChatGPT, Perplexity, Claude, Bing)
8. **Check for citations** and note what gets quoted
9. **Review analytics** for traffic and engagement

### Ongoing (Monthly)

10. **Track citations** in spreadsheet
11. **Run A/B tests** on content strategies
12. **Update content** based on what works
13. **Refresh dateModified** when updating

---

## Quick Reference: Validation Tools

| Tool | Purpose | URL |
|------|---------|-----|
| Schema.org Validator | Validate JSON-LD syntax | https://validator.schema.org/ |
| Google Rich Results Test | Check Google's schema parsing | https://search.google.com/test/rich-results |
| JSONLint | Validate JSON syntax | https://jsonlint.com/ |
| Hemingway Editor | Check readability | https://hemingwayapp.com/ |
| validate_aieo.py | Automated AIEO checks | `python scripts/validate_aieo.py` |

---

## What Good Validation Looks Like

**✅ Green flags:**
- All schema validates without errors
- Single H1 with clear topic
- 80%+ of content in lists or tables
- Every image has descriptive alt text
- No "click here" links
- Author and date clearly shown
- AI engines cite your content within 2 weeks

**❌ Red flags:**
- Schema validation errors
- Multiple H1s or no H1
- Walls of text (no lists or structure)
- Missing alt text
- Generic link text
- No author or date
- Zero AI citations after 1 month

---

Need help fixing validation errors? Reference:
- `SKILL.md` for AIEO principles
- `schema-reference.md` for schema fixes
- `content-templates.md` for structure examples

