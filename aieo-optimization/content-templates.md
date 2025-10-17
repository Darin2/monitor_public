# AIEO Content Templates

Ready-to-use templates for AI-optimized content. Each template includes structure guidelines, example content, schema markup, and checklists.

## Table of Contents

1. [Product/Tool Page Template](#producttool-page-template)
2. [Tutorial/Guide Template](#tutorialguide-template)
3. [Comparison/Review Template](#comparisonreview-template)
4. [Blog Post Template](#blog-post-template)
5. [Landing Page Template](#landing-page-template)

---

## Product/Tool Page Template

**Use for:** SaaS tools, plugins, downloadable software, services, apps

### Structure

```
H1: [Tool Name] – [One-sentence value proposition]
├── Definition paragraph (2-3 sentences)
├── Key Features (bulleted list)
├── How It Works (numbered steps or process)
├── Use Cases / Who It's For (bulleted list)
├── Pricing Table
├── Comparison Table (vs alternatives)
└── FAQ Section
```

### Example Content

```html
<!DOCTYPE html>
<html>
<head>
    <title>TaskFlow – Simple Task Management for Indie Developers</title>
    <!-- Product Schema -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "SoftwareApplication",
      "name": "TaskFlow",
      "applicationCategory": "ProductivityApplication",
      "description": "TaskFlow is a lightweight task management tool designed for indie developers and small teams. Track projects, manage deadlines, and visualize progress without enterprise complexity.",
      "operatingSystem": "Web, macOS, Windows, Linux",
      "offers": {
        "@type": "Offer",
        "price": "12.00",
        "priceCurrency": "USD",
        "priceValidUntil": "2025-12-31"
      },
      "featureList": [
        "Kanban boards with drag-and-drop",
        "Time tracking and estimates",
        "GitHub integration",
        "Markdown support",
        "Offline mode"
      ],
      "screenshot": "https://yoursite.com/taskflow-screenshot.png"
    }
    </script>
    <!-- FAQ Schema -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "FAQPage",
      "mainEntity": [
        {
          "@type": "Question",
          "name": "Does TaskFlow work offline?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Yes, TaskFlow includes offline mode. Changes sync automatically when you reconnect."
          }
        }
      ]
    }
    </script>
</head>
<body>

<article>
  <h1>TaskFlow – Simple Task Management for Indie Developers</h1>
  
  <!-- Definition-first opener -->
  <p>
    TaskFlow is a lightweight task management tool designed for indie developers and small teams. 
    Unlike enterprise tools like Jira or Asana, TaskFlow focuses on simplicity: track projects, 
    manage deadlines, and visualize progress without the complexity.
  </p>

  <h2>Key Features</h2>
  <ul>
    <li><strong>Kanban boards</strong> – Drag-and-drop interface with customizable columns</li>
    <li><strong>Time tracking</strong> – Built-in timer with estimates vs. actuals</li>
    <li><strong>GitHub integration</strong> – Sync issues and PRs automatically</li>
    <li><strong>Markdown everywhere</strong> – Full Markdown support in tasks and comments</li>
    <li><strong>Offline mode</strong> – Work without internet, syncs when reconnected</li>
    <li><strong>Dark mode</strong> – Easy on the eyes during late-night coding sessions</li>
  </ul>

  <h2>How It Works</h2>
  <ol>
    <li><strong>Create a project</strong> – Add a new board for each project or client</li>
    <li><strong>Add tasks</strong> – Create cards with titles, descriptions, and estimates</li>
    <li><strong>Organize with columns</strong> – Use default columns (To Do, In Progress, Done) or create custom ones</li>
    <li><strong>Track time</strong> – Start the timer when you begin work on a task</li>
    <li><strong>Review progress</strong> – See velocity charts and time reports</li>
  </ol>

  <h2>Who It's For</h2>
  <ul>
    <li>Indie developers managing multiple side projects</li>
    <li>Freelancers tracking client work</li>
    <li>Small teams (2-10 people) who find Jira overwhelming</li>
    <li>Developers who want task management without leaving their workflow</li>
  </ul>

  <h2>Pricing</h2>
  <table>
    <thead>
      <tr>
        <th>Plan</th>
        <th>Price</th>
        <th>Projects</th>
        <th>Team Size</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Solo</td>
        <td>$12/month</td>
        <td>Unlimited</td>
        <td>1 user</td>
      </tr>
      <tr>
        <td>Team</td>
        <td>$29/month</td>
        <td>Unlimited</td>
        <td>Up to 10 users</td>
      </tr>
    </tbody>
  </table>

  <h2>TaskFlow vs. Alternatives</h2>
  <table>
    <thead>
      <tr>
        <th>Feature</th>
        <th>TaskFlow</th>
        <th>Trello</th>
        <th>Jira</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Offline mode</td>
        <td>✅ Yes</td>
        <td>❌ No</td>
        <td>❌ No</td>
      </tr>
      <tr>
        <td>Time tracking</td>
        <td>✅ Built-in</td>
        <td>⚠️ Via Power-Ups</td>
        <td>✅ Built-in</td>
      </tr>
      <tr>
        <td>GitHub integration</td>
        <td>✅ Native</td>
        <td>⚠️ Via Power-Ups</td>
        <td>✅ Native</td>
      </tr>
      <tr>
        <td>Complexity</td>
        <td>Simple</td>
        <td>Simple</td>
        <td>Complex</td>
      </tr>
      <tr>
        <td>Price (solo)</td>
        <td>$12/mo</td>
        <td>Free/$5/mo</td>
        <td>$7.75/mo</td>
      </tr>
    </tbody>
  </table>

  <h2>Frequently Asked Questions</h2>
  
  <h3>Does TaskFlow work offline?</h3>
  <p>Yes, TaskFlow includes offline mode. Changes sync automatically when you reconnect.</p>

  <h3>Can I import from Trello or Jira?</h3>
  <p>Yes, TaskFlow supports importing boards from Trello (JSON export) and Jira (CSV export).</p>

  <h3>Is there a free trial?</h3>
  <p>Yes, all plans include a 14-day free trial with no credit card required.</p>

  <h3>What integrations are available?</h3>
  <p>TaskFlow integrates with GitHub, GitLab, Slack, and Zapier. API access is available on all plans.</p>

</article>

</body>
</html>
```

### Content Checklist

- [ ] H1 includes tool name and value proposition
- [ ] Opening paragraph defines what it is and who it's for
- [ ] Key features as bulleted list (5-8 items)
- [ ] "How It Works" section with numbered steps
- [ ] "Who It's For" section with specific use cases
- [ ] Pricing table with clear comparison
- [ ] Comparison table vs. 2-3 alternatives
- [ ] FAQ section (4-6 questions)
- [ ] Product schema with pricing and features
- [ ] FAQ schema for questions section
- [ ] Screenshots/images with descriptive alt text
- [ ] Clear call-to-action (sign up, try free, etc.)

---

## Tutorial/Guide Template

**Use for:** How-to guides, setup instructions, step-by-step tutorials, recipes

### Structure

```
H1: How to [Achieve Goal]
├── Brief intro (what you'll learn, time required)
├── Prerequisites / What You Need
├── Step 1: [Action]
├── Step 2: [Action]
├── Step N: [Action]
├── Troubleshooting (common issues)
└── Next Steps / Further Reading
```

### Example Content

```html
<!DOCTYPE html>
<html>
<head>
    <title>How to Deploy a Next.js App to Vercel in 5 Minutes</title>
    <!-- HowTo Schema -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "HowTo",
      "name": "Deploy a Next.js App to Vercel",
      "description": "Step-by-step guide to deploying your Next.js application to Vercel hosting in under 5 minutes.",
      "totalTime": "PT5M",
      "supply": [
        {"@type": "HowToSupply", "name": "Next.js application"},
        {"@type": "HowToSupply", "name": "GitHub account"},
        {"@type": "HowToSupply", "name": "Vercel account"}
      ],
      "tool": [
        {"@type": "HowToTool", "name": "Git"}
      ],
      "step": [
        {
          "@type": "HowToStep",
          "position": 1,
          "name": "Push code to GitHub",
          "text": "Create a GitHub repository and push your Next.js project code.",
          "url": "https://yoursite.com/deploy-nextjs#step-1"
        },
        {
          "@type": "HowToStep",
          "position": 2,
          "name": "Connect Vercel to GitHub",
          "text": "Sign in to Vercel, click Import Project, and authorize GitHub access.",
          "url": "https://yoursite.com/deploy-nextjs#step-2"
        },
        {
          "@type": "HowToStep",
          "position": 3,
          "name": "Deploy",
          "text": "Select your repository, review auto-detected settings, and click Deploy.",
          "url": "https://yoursite.com/deploy-nextjs#step-3"
        }
      ]
    }
    </script>
    <!-- Article Schema -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Article",
      "headline": "How to Deploy a Next.js App to Vercel in 5 Minutes",
      "author": {"@type": "Person", "name": "Darin Hardin"},
      "datePublished": "2025-01-15",
      "dateModified": "2025-01-15"
    }
    </script>
</head>
<body>

<article>
  <h1>How to Deploy a Next.js App to Vercel in 5 Minutes</h1>
  
  <p>
    This guide walks you through deploying a Next.js application to Vercel, 
    the platform created by Next.js's creators. The entire process takes under 5 minutes.
  </p>

  <p><strong>Time required:</strong> 5 minutes</p>

  <h2>What You Need</h2>
  <ul>
    <li>A Next.js application (created with <code>npx create-next-app</code>)</li>
    <li>A <a href="https://github.com">GitHub account</a></li>
    <li>A free <a href="https://vercel.com">Vercel account</a></li>
    <li>Git installed on your computer</li>
  </ul>

  <h2 id="step-1">Step 1: Push Your Code to GitHub</h2>
  <p>If your Next.js project isn't already on GitHub:</p>
  
  <ol>
    <li>Go to <a href="https://github.com/new">GitHub</a> and create a new repository</li>
    <li>Open terminal in your Next.js project directory</li>
    <li>Run these commands:</li>
  </ol>

  <pre><code>git init
git add .
git commit -m "Initial commit"
git remote add origin https://github.com/yourusername/your-repo.git
git push -u origin main</code></pre>

  <p><strong>Result:</strong> Your code is now on GitHub.</p>

  <h2 id="step-2">Step 2: Connect Vercel to GitHub</h2>
  
  <ol>
    <li>Go to <a href="https://vercel.com">vercel.com</a> and sign in (or sign up with GitHub)</li>
    <li>Click the <strong>"Add New Project"</strong> button</li>
    <li>Click <strong>"Import Git Repository"</strong></li>
    <li>Authorize Vercel to access your GitHub account</li>
    <li>Select the repository you just created</li>
  </ol>

  <p><strong>Result:</strong> Vercel can now access your GitHub repository.</p>

  <h2 id="step-3">Step 3: Configure and Deploy</h2>
  
  <ol>
    <li>Vercel will auto-detect that you're using Next.js</li>
    <li>Review the default settings:
      <ul>
        <li><strong>Framework Preset:</strong> Next.js (auto-detected)</li>
        <li><strong>Build Command:</strong> <code>npm run build</code></li>
        <li><strong>Output Directory:</strong> <code>.next</code></li>
      </ul>
    </li>
    <li>Click <strong>"Deploy"</strong></li>
  </ol>

  <p>Vercel will now:</p>
  <ul>
    <li>Install your dependencies</li>
    <li>Run your build</li>
    <li>Deploy to a production URL</li>
  </ul>

  <p><strong>Result:</strong> Your app is live! You'll get a URL like <code>your-app.vercel.app</code>.</p>

  <h2>What Happens Next</h2>
  <p>
    Every time you push to your GitHub repository's main branch, Vercel will 
    automatically rebuild and redeploy your app. Pull requests get preview deployments.
  </p>

  <h2>Troubleshooting</h2>

  <h3>Build fails with "Cannot find module"</h3>
  <p><strong>Cause:</strong> Missing dependency in package.json</p>
  <p><strong>Fix:</strong> Run <code>npm install [package]</code> locally, commit, and push.</p>

  <h3>Environment variables not working</h3>
  <p><strong>Cause:</strong> Environment variables need to be set in Vercel</p>
  <p><strong>Fix:</strong> Go to Project Settings → Environment Variables in Vercel dashboard</p>

  <h3>404 on dynamic routes</h3>
  <p><strong>Cause:</strong> Static export mode doesn't support dynamic routes</p>
  <p><strong>Fix:</strong> Remove <code>output: 'export'</code> from next.config.js</p>

  <h2>Next Steps</h2>
  <ul>
    <li><a href="https://vercel.com/docs/custom-domains">Add a custom domain</a></li>
    <li><a href="https://vercel.com/docs/analytics">Set up Vercel Analytics</a></li>
    <li><a href="https://vercel.com/docs/environment-variables">Configure environment variables</a></li>
  </ul>

</article>

</body>
</html>
```

### Content Checklist

- [ ] H1 starts with "How to [goal]"
- [ ] Opening paragraph states what you'll learn and time required
- [ ] "What You Need" section lists prerequisites
- [ ] Each step has a clear H2 heading with ID anchor
- [ ] Steps use numbered lists for sub-steps
- [ ] Each step ends with "Result:" statement
- [ ] Code blocks use `<pre><code>` with syntax highlighting
- [ ] Troubleshooting section addresses 3-5 common issues
- [ ] "Next Steps" section for further learning
- [ ] HowTo schema with all steps
- [ ] Article schema with author and date

---

## Comparison/Review Template

**Use for:** Tool comparisons, product reviews, "X vs. Y" posts

### Structure

```
H1: [Tool A] vs [Tool B]: Which [Category] Is Best for [Audience]?
├── Summary / TL;DR (verdict upfront)
├── Overview of Tool A
├── Overview of Tool B
├── Feature Comparison Table
├── Pricing Comparison
├── Pros and Cons (side by side)
├── When to Choose Tool A
├── When to Choose Tool B
└── Final Recommendation
```

### Example Content

```html
<!DOCTYPE html>
<html>
<head>
    <title>Notion vs. Obsidian: Which Note-Taking App Is Best for Developers?</title>
    <!-- Article Schema -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Article",
      "headline": "Notion vs. Obsidian: Which Note-Taking App Is Best for Developers?",
      "description": "Detailed comparison of Notion and Obsidian for developer note-taking, including features, pricing, pros and cons.",
      "author": {"@type": "Person", "name": "Darin Hardin"},
      "datePublished": "2025-01-15",
      "dateModified": "2025-01-15"
    }
    </script>
</head>
<body>

<article>
  <h1>Notion vs. Obsidian: Which Note-Taking App Is Best for Developers?</h1>
  
  <p><strong>TL;DR:</strong> Choose Notion if you want an all-in-one workspace with databases and team collaboration. 
  Choose Obsidian if you want local-first storage, Markdown files, and powerful linking.</p>

  <h2>Overview: Notion</h2>
  <p>
    Notion is a cloud-based workspace that combines notes, databases, wikis, and project management. 
    It uses a block-based editor and stores everything in proprietary format on Notion's servers.
  </p>
  <ul>
    <li><strong>Type:</strong> Cloud-based, all-in-one workspace</li>
    <li><strong>Storage:</strong> Notion's servers (proprietary format)</li>
    <li><strong>Best for:</strong> Teams, databases, project management</li>
  </ul>

  <h2>Overview: Obsidian</h2>
  <p>
    Obsidian is a local-first note-taking app that works with plain Markdown files. 
    It emphasizes linking between notes and building a "second brain" knowledge graph.
  </p>
  <ul>
    <li><strong>Type:</strong> Local-first, Markdown-based</li>
    <li><strong>Storage:</strong> Your computer (plain .md files)</li>
    <li><strong>Best for:</strong> Personal knowledge management, privacy, long-term storage</li>
  </ul>

  <h2>Feature Comparison</h2>
  <table>
    <thead>
      <tr>
        <th>Feature</th>
        <th>Notion</th>
        <th>Obsidian</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><strong>Storage</strong></td>
        <td>Cloud (Notion servers)</td>
        <td>Local (your files)</td>
      </tr>
      <tr>
        <td><strong>File format</strong></td>
        <td>Proprietary</td>
        <td>Markdown (.md)</td>
      </tr>
      <tr>
        <td><strong>Offline access</strong></td>
        <td>Limited (cached pages)</td>
        <td>Full (local files)</td>
      </tr>
      <tr>
        <td><strong>Linking notes</strong></td>
        <td>Basic [[links]]</td>
        <td>Advanced backlinks + graph</td>
      </tr>
      <tr>
        <td><strong>Databases</strong></td>
        <td>✅ Built-in</td>
        <td>⚠️ Via plugins (Dataview)</td>
      </tr>
      <tr>
        <td><strong>Team collaboration</strong></td>
        <td>✅ Real-time</td>
        <td>❌ Manual sync only</td>
      </tr>
      <tr>
        <td><strong>Plugin ecosystem</strong></td>
        <td>Limited integrations</td>
        <td>1000+ community plugins</td>
      </tr>
      <tr>
        <td><strong>Mobile apps</strong></td>
        <td>✅ iOS, Android</td>
        <td>✅ iOS, Android</td>
      </tr>
      <tr>
        <td><strong>API access</strong></td>
        <td>✅ Yes</td>
        <td>❌ No official API</td>
      </tr>
    </tbody>
  </table>

  <h2>Pricing Comparison</h2>
  <table>
    <thead>
      <tr>
        <th>Plan</th>
        <th>Notion</th>
        <th>Obsidian</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><strong>Free plan</strong></td>
        <td>Limited blocks</td>
        <td>Unlimited (local only)</td>
      </tr>
      <tr>
        <td><strong>Personal</strong></td>
        <td>$10/month</td>
        <td>Free (local)</td>
      </tr>
      <tr>
        <td><strong>Sync service</strong></td>
        <td>Included</td>
        <td>$10/month (Obsidian Sync)</td>
      </tr>
      <tr>
        <td><strong>Team</strong></td>
        <td>$18/user/month</td>
        <td>N/A</td>
      </tr>
    </tbody>
  </table>

  <h2>Notion: Pros and Cons</h2>
  
  <h3>✅ Pros</h3>
  <ul>
    <li>All-in-one workspace (notes + tasks + databases)</li>
    <li>Great for team collaboration</li>
    <li>Beautiful, polished interface</li>
    <li>Powerful database views (table, kanban, calendar)</li>
    <li>Works seamlessly across devices</li>
  </ul>

  <h3>❌ Cons</h3>
  <ul>
    <li>Proprietary format (vendor lock-in risk)</li>
    <li>Requires internet connection for full functionality</li>
    <li>Can feel slow with large workspaces</li>
    <li>Export options are limited</li>
    <li>No local-first option</li>
  </ul>

  <h2>Obsidian: Pros and Cons</h2>
  
  <h3>✅ Pros</h3>
  <ul>
    <li>Local-first (you own your data)</li>
    <li>Plain Markdown files (future-proof)</li>
    <li>Works 100% offline</li>
    <li>Powerful linking and backlinks</li>
    <li>Massive plugin ecosystem</li>
    <li>Free for personal use</li>
  </ul>

  <h3>❌ Cons</h3>
  <ul>
    <li>No built-in collaboration</li>
    <li>Steeper learning curve</li>
    <li>Database features require plugins</li>
    <li>Manual sync setup (without Obsidian Sync)</li>
    <li>Less polished UI than Notion</li>
  </ul>

  <h2>When to Choose Notion</h2>
  <p>Pick Notion if you:</p>
  <ul>
    <li>Work with a team and need real-time collaboration</li>
    <li>Want an all-in-one tool (notes + tasks + databases + wiki)</li>
    <li>Prefer cloud-based tools with automatic syncing</li>
    <li>Use databases heavily (project tracking, CRM, etc.)</li>
    <li>Value polish and UI beauty over customization</li>
  </ul>

  <h2>When to Choose Obsidian</h2>
  <p>Pick Obsidian if you:</p>
  <ul>
    <li>Want to own your data (local files)</li>
    <li>Prefer Markdown and plain text</li>
    <li>Need offline access 100% of the time</li>
    <li>Build a personal knowledge base (second brain)</li>
    <li>Want powerful plugins and customization</li>
    <li>Are privacy-conscious</li>
  </ul>

  <h2>Final Recommendation</h2>
  <p>
    <strong>For developers:</strong> I recommend Obsidian for personal note-taking and knowledge management. 
    The local-first approach, Markdown format, and plugin ecosystem make it ideal for technical notes, 
    code snippets, and building a developer knowledge base.
  </p>
  <p>
    <strong>For teams:</strong> Notion is the better choice if you need collaboration features, 
    shared databases, and a centralized workspace for project management.
  </p>
  <p>
    <strong>My setup:</strong> I use Obsidian for personal notes and learning, and Notion for 
    team projects and client work.
  </p>

</article>

</body>
</html>
```

### Content Checklist

- [ ] H1 includes both tools and target audience
- [ ] TL;DR at the top with clear verdict
- [ ] Overview section for each tool (2-3 sentences + key facts)
- [ ] Feature comparison table with 8-12 rows
- [ ] Pricing comparison table
- [ ] Pros and cons lists for each tool (5-7 items each)
- [ ] "When to choose X" sections with specific use cases
- [ ] Final recommendation with personal experience/opinion
- [ ] Article schema with publish date
- [ ] No affiliate disclosures if using affiliate links

---

## Blog Post Template

**Use for:** Opinion pieces, announcements, case studies, thought leadership

### Structure

```
H1: [Compelling Title with Clear Benefit]
├── Hook / Opening (problem or interesting statement)
├── Context / Background
├── Main Points (H2 sections, each with sub-points)
├── Examples / Case Studies
├── Actionable Takeaways
└── Conclusion / Call to Action
```

### Example Content

```html
<!DOCTYPE html>
<html>
<head>
    <title>I Built 12 Side Projects in 2024. Here's What I Learned About Launching Fast</title>
    <!-- Article Schema -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Article",
      "headline": "I Built 12 Side Projects in 2024. Here's What I Learned About Launching Fast",
      "description": "Lessons learned from shipping 12 indie projects in one year, including what worked, what didn't, and how to launch faster.",
      "author": {
        "@type": "Person",
        "name": "Darin Hardin",
        "url": "https://yoursite.com/about"
      },
      "publisher": {
        "@type": "Organization",
        "name": "Your Site Name",
        "logo": {"@type": "ImageObject", "url": "https://yoursite.com/logo.png"}
      },
      "datePublished": "2025-01-15",
      "dateModified": "2025-01-15",
      "image": "https://yoursite.com/12-projects-cover.jpg"
    }
    </script>
</head>
<body>

<article>
  <h1>I Built 12 Side Projects in 2024. Here's What I Learned About Launching Fast</h1>
  
  <p><time datetime="2025-01-15">January 15, 2025</time> • 8 min read</p>

  <p>
    Last January, I set a goal: ship 12 side projects in 12 months. Not ideas. Not prototypes. 
    <em>Launched, public projects with real users.</em>
  </p>

  <p>
    I hit the goal. Some projects succeeded (1K+ users). Most failed (< 10 users). 
    But the process taught me more about building and launching than the previous 5 years combined.
  </p>

  <h2>Why 12 Projects?</h2>
  <p>
    Like many developers, I had a graveyard of unfinished projects. Beautiful code. Perfect architecture. 
    <strong>Zero users.</strong>
  </p>

  <p>The problem wasn't technical skill—it was shipping. I'd spend months perfecting features nobody asked for.</p>

  <p>The 12-project challenge forced a new constraint: <strong>launch within 1 month or kill the project.</strong></p>

  <h2>The Projects</h2>
  <table>
    <thead>
      <tr>
        <th>Month</th>
        <th>Project</th>
        <th>Users (month 1)</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <tr><td>Jan</td><td>ReadNotes (book summaries)</td><td>8</td><td>❌ Shut down</td></tr>
      <tr><td>Feb</td><td>DailyCommit (GitHub streak tracker)</td><td>124</td><td>✅ Active</td></tr>
      <tr><td>Mar</td><td>CodeSnippets (snippet manager)</td><td>43</td><td>❌ Shut down</td></tr>
      <tr><td>Apr</td><td>TaskFlow (task manager)</td><td>1,203</td><td>✅ Active (revenue)</td></tr>
      <tr><td>May</td><td>QuickPoll (polling tool)</td><td>67</td><td>❌ Shut down</td></tr>
      <tr><td>Jun</td><td>DevJobs (job board)</td><td>412</td><td>✅ Active</td></tr>
      <tr><td>Jul</td><td>LinkTree clone</td><td>89</td><td>❌ Shut down</td></tr>
      <tr><td>Aug</td><td>MarkdownBlog (CMS)</td><td>234</td><td>✅ Active</td></tr>
      <tr><td>Sep</td><td>TinyAnalytics</td><td>156</td><td>✅ Active</td></tr>
      <tr><td>Oct</td><td>WeatherAPI wrapper</td><td>22</td><td>❌ Shut down</td></tr>
      <tr><td>Nov</td><td>GithubProfile generator</td><td>891</td><td>✅ Active</td></tr>
      <tr><td>Dec</td><td>IndieStack (tool directory)</td><td>345</td><td>✅ Active</td></tr>
    </tbody>
  </table>

  <h2>Lesson 1: Launch at 60%, Not 100%</h2>
  <p>
    My most successful project (TaskFlow, 1,200 users) launched with:
  </p>
  <ul>
    <li>No user accounts (local storage only)</li>
    <li>No mobile app</li>
    <li>No time tracking (added month 2)</li>
    <li>Bugs in edge cases</li>
  </ul>

  <p><strong>It worked because it solved one problem well: simple task boards.</strong></p>

  <p>
    My biggest failures launched with 20 features. Nobody cared about 19 of them.
  </p>

  <p><strong>Takeaway:</strong> Launch with the minimum feature set that solves the core problem. Add more only after real users ask.</p>

  <h2>Lesson 2: Talk to Users Before Building</h2>
  <p>
    I built ReadNotes (book summaries) based on my own need. Launched to crickets. 
    Turned out: people who read enough to want summaries don't need them.
  </p>

  <p>
    For TaskFlow, I posted "What's missing from your task manager?" in 5 developer communities. 
    Got 200+ responses. Built exactly what they asked for.
  </p>

  <p><strong>Takeaway:</strong> Validate the problem before writing code. Post in communities, run polls, check forum complaints.</p>

  <h2>Lesson 3: Pick Small, Boring Problems</h2>
  <p>
    My successful projects solved boring problems:
  </p>
  <ul>
    <li><strong>TaskFlow:</strong> Simple kanban boards (boring, but needed)</li>
    <li><strong>DevJobs:</strong> Job board with salary ranges (boring, but transparent)</li>
    <li><strong>GithubProfile:</strong> Generate nice README profiles (boring, but useful)</li>
  </ul>

  <p>
    My failures tried to be clever or revolutionary. Nobody wants revolutionary task management. 
    They want their tasks organized <em>today</em>.
  </p>

  <p><strong>Takeaway:</strong> Solve mundane problems. "10% better" beats "100% different."</p>

  <h2>Lesson 4: Distribution > Product (Early On)</h2>
  <p>
    I spent 80% of time building, 20% marketing. Should have been 50/50 (or even 40/60).
  </p>

  <p>Great projects with zero distribution: <strong>zero users.</strong></p>

  <p>Mediocre projects with good distribution: <strong>hundreds of users.</strong></p>

  <p>Where I found users:</p>
  <ul>
    <li>Reddit (post in niche communities, not r/SideProject)</li>
    <li>Indie Hackers (weekly showcase threads)</li>
    <li>Product Hunt (only worked for 1 project)</li>
    <li>Dev.to and Hashnode (write tutorials using your tool)</li>
    <li>Twitter (share progress, not just launch)</li>
  </ul>

  <p><strong>Takeaway:</strong> Plan distribution from day 1. Know where your first 100 users will come from before you write code.</p>

  <h2>Lesson 5: Kill Projects Fast</h2>
  <p>
    I shut down 6 projects within 3 months of launch. It hurt, but it freed time for winners.
  </p>

  <p>My rule: if a project has fewer than 20 weekly active users after 2 months, kill it or pivot.</p>

  <p>Don't fall for sunk cost fallacy. That beautifully architected codebase nobody uses? It's waste.</p>

  <p><strong>Takeaway:</strong> Set kill criteria before launch. Stick to them.</p>

  <h2>What I'd Do Differently</h2>
  <ul>
    <li><strong>Build in public more:</strong> Share progress weekly, not just at launch</li>
    <li><strong>Start with distribution:</strong> Build audience before building product</li>
    <li><strong>Charge earlier:</strong> Even $5/mo filters out tire-kickers</li>
    <li><strong>Copy more:</strong> "X but with Y" is a valid strategy</li>
    <li><strong>Use no-code for MVPs:</strong> Reserve code for proven ideas</li>
  </ul>

  <h2>Final Thoughts</h2>
  <p>
    Shipping 12 projects taught me that ideas are cheap and execution is hard—but <strong>distribution is harder</strong>.
  </p>

  <p>
    The best project idea with zero users is worth less than a mediocre idea with 1,000 users.
  </p>

  <p>
    If you're stuck in tutorial hell or have a graveyard of unfinished projects, try this: 
    <strong>commit to shipping something—anything—in 30 days.</strong> Make it public. Get feedback. Iterate or kill.
  </p>

  <p>Repeat 12 times. You'll learn more than any course can teach.</p>

  <hr>

  <p><em>What's your biggest challenge with shipping side projects? Reply on <a href="https://twitter.com/yourhandle">Twitter</a>.</em></p>

</article>

</body>
</html>
```

### Content Checklist

- [ ] H1 is specific and benefit-driven
- [ ] Opening hook grabs attention (surprising stat, story, problem)
- [ ] Article schema with author, date, publisher
- [ ] Subheadings (H2) break up content every 200-400 words
- [ ] Use lists and tables for data/examples
- [ ] Include specific numbers and examples
- [ ] Personal voice (I/we, not generic "you should")
- [ ] Actionable takeaways (readers can do something)
- [ ] Conclusion ties back to opening
- [ ] Call to action at end (comment, share, try something)

---

## Landing Page Template

**Use for:** Product launches, lead generation, event registration

### Structure

```
Above Fold:
├── H1: Clear value proposition
├── Subheading: Who it's for and key benefit
├── Primary CTA button
└── Hero image/demo

Below Fold:
├── Social proof (logos, testimonials)
├── Features (3-5 key features with icons)
├── How It Works (3-step process)
├── Benefits (outcome-focused)
├── Pricing (if applicable)
├── FAQ
└── Final CTA
```

### Example Content

```html
<!DOCTYPE html>
<html>
<head>
    <title>TaskFlow – Simple Task Management for Indie Developers</title>
    <meta name="description" content="Lightweight task boards with time tracking, GitHub integration, and offline mode. Built for indie developers who find Jira overwhelming.">
    <!-- Product Schema (see schema-reference.md) -->
</head>
<body>

<!-- Hero Section -->
<section class="hero">
  <h1>Stop Juggling Tasks in Your Head</h1>
  <p>
    TaskFlow is task management for indie developers. 
    Track projects, manage deadlines, and visualize progress—without enterprise complexity.
  </p>
  <button>Start Free Trial – No Credit Card</button>
  <img src="hero-screenshot.png" alt="TaskFlow kanban board interface showing three columns: To Do, In Progress, and Done">
</section>

<!-- Social Proof -->
<section class="social-proof">
  <p>Trusted by 1,200+ indie developers and small teams</p>
  <!-- Logos or user avatars -->
</section>

<!-- Features -->
<section class="features">
  <h2>Everything You Need, Nothing You Don't</h2>
  
  <div class="feature">
    <h3>📋 Kanban Boards</h3>
    <p>Drag-and-drop cards with customizable columns. Organize by project, client, or sprint.</p>
  </div>

  <div class="feature">
    <h3>⏱️ Time Tracking</h3>
    <p>Built-in timer tracks time spent on each task. See estimates vs. actuals.</p>
  </div>

  <div class="feature">
    <h3>🔗 GitHub Integration</h3>
    <p>Sync issues and pull requests automatically. No manual updates.</p>
  </div>

  <div class="feature">
    <h3>✈️ Offline Mode</h3>
    <p>Work without internet. Changes sync when you reconnect.</p>
  </div>
</section>

<!-- How It Works -->
<section class="how-it-works">
  <h2>Get Started in 3 Steps</h2>
  
  <ol>
    <li>
      <strong>Create a board</strong> – Add a project in 10 seconds
    </li>
    <li>
      <strong>Add tasks</strong> – Drop in cards with titles and estimates
    </li>
    <li>
      <strong>Start tracking</strong> – Hit the timer when you begin work
    </li>
  </ol>
</section>

<!-- Benefits (Outcome-Focused) -->
<section class="benefits">
  <h2>Finally See What You're Actually Working On</h2>
  
  <ul>
    <li>✅ <strong>Stop context switching</strong> – See all projects in one place</li>
    <li>✅ <strong>Ship faster</strong> – Clear visibility prevents scope creep</li>
    <li>✅ <strong>Track your time</strong> – Know where hours actually go</li>
    <li>✅ <strong>Work offline</strong> – Code on planes, trains, or coffee shops</li>
  </ul>
</section>

<!-- Testimonials -->
<section class="testimonials">
  <h2>What Developers Are Saying</h2>
  
  <blockquote>
    "TaskFlow is what Trello should have been for developers. Offline mode is a game-changer."
    <cite>– Sarah K., Freelance Developer</cite>
  </blockquote>

  <blockquote>
    "Finally ditched Jira. TaskFlow does everything I need without the bloat."
    <cite>– Mike T., Indie SaaS Founder</cite>
  </blockquote>
</section>

<!-- Pricing -->
<section class="pricing">
  <h2>Simple, Transparent Pricing</h2>
  
  <div class="plan">
    <h3>Solo</h3>
    <p class="price">$12/month</p>
    <ul>
      <li>Unlimited projects</li>
      <li>Time tracking</li>
      <li>GitHub integration</li>
      <li>Offline mode</li>
    </ul>
    <button>Start Free Trial</button>
  </div>

  <div class="plan featured">
    <h3>Team</h3>
    <p class="price">$29/month</p>
    <ul>
      <li>Everything in Solo</li>
      <li>Up to 10 team members</li>
      <li>Shared boards</li>
      <li>Activity timeline</li>
    </ul>
    <button>Start Free Trial</button>
  </div>
</section>

<!-- FAQ -->
<section class="faq">
  <h2>Frequently Asked Questions</h2>
  
  <h3>Is there a free trial?</h3>
  <p>Yes, all plans include a 14-day free trial. No credit card required.</p>

  <h3>Can I cancel anytime?</h3>
  <p>Yes, cancel anytime with one click. No questions asked.</p>

  <h3>Do you offer refunds?</h3>
  <p>Yes, full refund within 30 days if you're not satisfied.</p>

  <h3>What about my data?</h3>
  <p>Export all your data anytime in JSON or CSV format. You own your data.</p>
</section>

<!-- Final CTA -->
<section class="final-cta">
  <h2>Start Organizing Your Projects Today</h2>
  <p>14-day free trial • No credit card • Cancel anytime</p>
  <button>Try TaskFlow Free</button>
</section>

</body>
</html>
```

### Content Checklist

- [ ] H1 focuses on outcome/benefit (not just "TaskFlow")
- [ ] Subheading clarifies who it's for
- [ ] Primary CTA above the fold (visible without scrolling)
- [ ] Hero image/demo shows the product in action
- [ ] Social proof (user count, testimonials, logos)
- [ ] 3-5 key features with icons and benefit-driven copy
- [ ] "How It Works" in 3 steps
- [ ] Benefits section focused on outcomes (not features)
- [ ] Pricing section with clear CTAs
- [ ] FAQ section (4-6 questions)
- [ ] Final CTA section that repeats key benefits
- [ ] Product schema markup
- [ ] FAQ schema markup
- [ ] Fast load time (< 2 seconds)

---

## General AIEO Checklist (All Templates)

Use this for every piece of content:

### Content Structure
- [ ] H1 includes primary keyword and clear benefit
- [ ] Opening paragraph defines what the content is (definition-first)
- [ ] Subheadings (H2, H3) create clear hierarchy
- [ ] Lists used instead of long paragraphs where possible
- [ ] Tables used for comparisons, features, pricing
- [ ] Short sentences (< 25 words average)
- [ ] One idea per paragraph

### Schema Markup
- [ ] Appropriate JSON-LD schema added (FAQ, HowTo, Article, Product)
- [ ] Schema validates at https://validator.schema.org/
- [ ] All required fields filled in schema
- [ ] Schema matches visible content (no hidden content)

### Metadata & Attribution
- [ ] Author name and bio linked
- [ ] Publish date and "last updated" date shown
- [ ] Meta description (150-160 characters)
- [ ] Images have descriptive alt text
- [ ] Internal links to related content

### Semantic HTML
- [ ] Proper heading hierarchy (H1 → H2 → H3, no skips)
- [ ] `<article>` tag for main content
- [ ] `<time>` tag for dates
- [ ] `<code>` and `<pre>` for code snippets
- [ ] Lists use `<ul>` or `<ol>`, not `<div>` bullets

### Quotability
- [ ] Key points can stand alone as complete thoughts
- [ ] Specific numbers and data points included
- [ ] Explicit comparisons ("Unlike X, Y does...")
- [ ] Clear cause-and-effect statements
- [ ] No fluff or filler sentences

---

Need help applying these templates? Reference `SKILL.md` for principles and `schema-reference.md` for schema examples.

