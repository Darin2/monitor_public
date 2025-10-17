#!/usr/bin/env python3
"""
AIEO Validation Script

Validates HTML content for AI Engine Optimization (AIEO) compliance.
Checks: schema markup, heading hierarchy, structured content, metadata, and more.

Usage:
    python validate_aieo.py <url or file>
    python validate_aieo.py https://example.com/page
    python validate_aieo.py path/to/file.html
"""

import sys
import json
import re
from urllib.parse import urlparse
from bs4 import BeautifulSoup
import requests

class AIEOValidator:
    def __init__(self, html_content, url=None):
        self.html = html_content
        self.soup = BeautifulSoup(html_content, 'html.parser')
        self.url = url
        self.results = {
            'passed': [],
            'warnings': [],
            'failed': [],
            'score': 0,
            'total_checks': 0
        }

    def validate_all(self):
        """Run all validation checks"""
        print("\nAIEO Validation Report")
        print("=" * 50)
        if self.url:
            print(f"URL: {self.url}\n")
        
        # Run all checks
        self.check_schema()
        self.check_headings()
        self.check_meta_description()
        self.check_author()
        self.check_dates()
        self.check_lists_and_tables()
        self.check_links()
        self.check_images()
        self.check_semantic_html()
        
        # Calculate score
        self.results['total_checks'] = len(self.results['passed']) + len(self.results['failed'])
        if self.results['total_checks'] > 0:
            self.results['score'] = (len(self.results['passed']) / self.results['total_checks']) * 100
        
        # Print results
        self.print_results()
        
        return self.results

    def check_schema(self):
        """Check for JSON-LD schema markup"""
        schemas = self.soup.find_all('script', {'type': 'application/ld+json'})
        
        if not schemas:
            self.fail("No JSON-LD schema found")
            return
        
        schema_types = []
        valid_schemas = 0
        
        for schema in schemas:
            try:
                data = json.loads(schema.string)
                
                # Handle array of schemas
                if isinstance(data, list):
                    for item in data:
                        if '@type' in item:
                            schema_types.append(item['@type'])
                            valid_schemas += 1
                else:
                    if '@type' in data:
                        schema_types.append(data['@type'])
                        valid_schemas += 1
                        
            except json.JSONDecodeError as e:
                self.fail(f"Invalid JSON-LD syntax: {e}")
                return
        
        if valid_schemas > 0:
            types_str = ', '.join(schema_types)
            self.passed(f"JSON-LD schema found ({types_str})")
            
            # Check for high-priority schema types
            if 'FAQPage' in schema_types:
                self.passed("FAQ schema detected (high AIEO value)")
            if 'HowTo' in schema_types:
                self.passed("HowTo schema detected (high AIEO value)")
        else:
            self.fail("JSON-LD found but no valid @type detected")

    def check_headings(self):
        """Check heading hierarchy and H1"""
        h1_tags = self.soup.find_all('h1')
        
        # Check H1 count
        if len(h1_tags) == 0:
            self.fail("No H1 heading found")
        elif len(h1_tags) > 1:
            self.fail(f"Multiple H1 headings found ({len(h1_tags)}). Should have exactly one.")
        else:
            h1_text = h1_tags[0].get_text().strip()
            if len(h1_text) < 10:
                self.warn(f"H1 is very short ({len(h1_text)} chars): '{h1_text}'")
            else:
                self.passed(f"Single H1 found: \"{h1_text[:60]}{'...' if len(h1_text) > 60 else ''}\"")
        
        # Check heading hierarchy
        headings = []
        for level in range(1, 7):
            for heading in self.soup.find_all(f'h{level}'):
                headings.append((level, heading.get_text().strip()))
        
        if len(headings) < 3:
            self.warn(f"Only {len(headings)} headings found. Consider adding more structure.")
        
        # Verify no skipped levels
        hierarchy_valid = True
        prev_level = 0
        for level, text in headings:
            if level - prev_level > 1 and prev_level != 0:
                self.fail(f"Heading hierarchy skipped from H{prev_level} to H{level}")
                hierarchy_valid = False
                break
            prev_level = level
        
        if hierarchy_valid and len(headings) >= 3:
            self.passed("Heading hierarchy is correct (no skipped levels)")

    def check_meta_description(self):
        """Check meta description"""
        meta_desc = self.soup.find('meta', {'name': 'description'})
        
        if not meta_desc or not meta_desc.get('content'):
            self.fail("No meta description found")
            return
        
        desc = meta_desc.get('content').strip()
        length = len(desc)
        
        if length < 120:
            self.warn(f"Meta description is short ({length} chars, ideal: 150-160)")
        elif length > 160:
            self.warn(f"Meta description is long ({length} chars, ideal: 150-160)")
        else:
            self.passed(f"Meta description length good ({length} chars)")

    def check_author(self):
        """Check for author information"""
        # Check for author in schema
        schemas = self.soup.find_all('script', {'type': 'application/ld+json'})
        author_in_schema = False
        
        for schema in schemas:
            try:
                data = json.loads(schema.string)
                if isinstance(data, list):
                    for item in data:
                        if 'author' in item:
                            author_in_schema = True
                else:
                    if 'author' in data:
                        author_in_schema = True
            except:
                pass
        
        # Check for author in HTML
        author_meta = self.soup.find('meta', {'name': 'author'})
        author_text = self.soup.find(string=re.compile(r'(by|author|written by)', re.I))
        
        if author_in_schema or author_meta or author_text:
            self.passed("Author information present")
        else:
            self.fail("No author information found")

    def check_dates(self):
        """Check for publish/modified dates"""
        # Check for date in schema
        schemas = self.soup.find_all('script', {'type': 'application/ld+json'})
        date_in_schema = False
        
        for schema in schemas:
            try:
                data = json.loads(schema.string)
                if isinstance(data, list):
                    for item in data:
                        if 'datePublished' in item or 'dateModified' in item:
                            date_in_schema = True
                else:
                    if 'datePublished' in data or 'dateModified' in data:
                        date_in_schema = True
            except:
                pass
        
        # Check for <time> tag
        time_tag = self.soup.find('time')
        
        if date_in_schema or time_tag:
            self.passed("Date information found")
        else:
            self.warn("No date information found (consider adding publish date)")

    def check_lists_and_tables(self):
        """Check for structured content (lists and tables)"""
        ul_count = len(self.soup.find_all('ul'))
        ol_count = len(self.soup.find_all('ol'))
        table_count = len(self.soup.find_all('table'))
        
        total_lists = ul_count + ol_count
        
        if total_lists == 0:
            self.fail("No lists found (consider using bulleted or numbered lists)")
        else:
            self.passed(f"Lists found ({ul_count} bulleted, {ol_count} numbered)")
        
        if table_count > 0:
            self.passed(f"Tables found ({table_count} tables)")
            
            # Check table structure
            for table in self.soup.find_all('table'):
                if not table.find('thead'):
                    self.warn("Table found without <thead> (consider adding for structure)")
                    break
        else:
            self.warn("No tables found (consider using tables for comparisons)")

    def check_links(self):
        """Check link quality (descriptive anchor text)"""
        links = self.soup.find_all('a', href=True)
        
        if len(links) < 3:
            self.warn(f"Only {len(links)} links found (consider adding internal links)")
        
        bad_link_texts = ['click here', 'here', 'learn more', 'read more', 'more']
        bad_links = []
        
        for link in links:
            text = link.get_text().strip().lower()
            if text in bad_link_texts:
                bad_links.append(f"\"{text}\" ({link.get('href')})")
        
        if bad_links:
            self.fail(f"Found {len(bad_links)} links with generic text: {', '.join(bad_links[:3])}")
        else:
            if len(links) > 0:
                self.passed(f"Link text quality good ({len(links)} links checked)")

    def check_images(self):
        """Check for alt text on images"""
        images = self.soup.find_all('img')
        
        if len(images) == 0:
            return  # No images, skip check
        
        missing_alt = []
        bad_alt = []
        
        for img in images:
            alt = img.get('alt', '').strip()
            src = img.get('src', 'unknown')
            
            if not alt:
                missing_alt.append(src)
            elif len(alt) < 10:
                bad_alt.append(f"{src} (alt: \"{alt}\")")
        
        if missing_alt:
            self.fail(f"{len(missing_alt)} images missing alt text")
        elif bad_alt:
            self.warn(f"{len(bad_alt)} images have very short alt text")
        else:
            self.passed(f"All images have alt text ({len(images)} images)")

    def check_semantic_html(self):
        """Check for semantic HTML usage"""
        has_article = bool(self.soup.find('article'))
        has_section = bool(self.soup.find('section'))
        
        if has_article:
            self.passed("Semantic HTML: <article> tag found")
        else:
            self.warn("No <article> tag found (consider wrapping main content)")
        
        # Check for <time> with datetime attribute
        time_tags = self.soup.find_all('time')
        if time_tags:
            has_datetime = any(tag.get('datetime') for tag in time_tags)
            if has_datetime:
                self.passed("Semantic HTML: <time> with datetime attribute")
            else:
                self.warn("<time> tag found but missing datetime attribute")

    def passed(self, message):
        """Record a passed check"""
        self.results['passed'].append(message)

    def warn(self, message):
        """Record a warning"""
        self.results['warnings'].append(message)

    def fail(self, message):
        """Record a failed check"""
        self.results['failed'].append(message)

    def print_results(self):
        """Print validation results"""
        print()
        
        # Passed checks
        for item in self.results['passed']:
            print(f"✅ PASSED: {item}")
        
        # Warnings
        for item in self.results['warnings']:
            print(f"⚠️  WARNING: {item}")
        
        # Failed checks
        for item in self.results['failed']:
            print(f"❌ FAILED: {item}")
        
        print()
        print("-" * 50)
        print(f"Overall Score: {len(self.results['passed'])}/{self.results['total_checks']} ({self.results['score']:.0f}%)")
        print()
        
        # Recommendations
        if self.results['failed'] or self.results['warnings']:
            print("Recommendations:")
            if self.results['failed']:
                print("- Fix all failed checks before publishing")
            if self.results['warnings']:
                print("- Review warnings and improve where possible")
        else:
            print("✨ Excellent! All AIEO checks passed.")
        
        print()


def fetch_url(url):
    """Fetch HTML content from URL"""
    try:
        headers = {
            'User-Agent': 'AIEO-Validator/1.0 (https://github.com/yourusername/aieo-validator)'
        }
        response = requests.get(url, headers=headers, timeout=10)
        response.raise_for_status()
        return response.text
    except requests.RequestException as e:
        print(f"Error fetching URL: {e}")
        sys.exit(1)


def read_file(filepath):
    """Read HTML content from file"""
    try:
        with open(filepath, 'r', encoding='utf-8') as f:
            return f.read()
    except FileNotFoundError:
        print(f"Error: File not found: {filepath}")
        sys.exit(1)
    except Exception as e:
        print(f"Error reading file: {e}")
        sys.exit(1)


def main():
    if len(sys.argv) < 2:
        print("Usage: python validate_aieo.py <url or file>")
        print("\nExamples:")
        print("  python validate_aieo.py https://example.com/page")
        print("  python validate_aieo.py path/to/file.html")
        sys.exit(1)
    
    target = sys.argv[1]
    
    # Determine if target is URL or file
    parsed = urlparse(target)
    is_url = parsed.scheme in ['http', 'https']
    
    if is_url:
        print(f"Fetching URL: {target}")
        html_content = fetch_url(target)
        url = target
    else:
        print(f"Reading file: {target}")
        html_content = read_file(target)
        url = None
    
    # Run validation
    validator = AIEOValidator(html_content, url)
    results = validator.validate_all()
    
    # Exit with appropriate code
    if len(results['failed']) > 0:
        sys.exit(1)
    else:
        sys.exit(0)


if __name__ == '__main__':
    main()

