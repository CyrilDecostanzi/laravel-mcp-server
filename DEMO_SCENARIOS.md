# Laravel MCP Server - Demo Scenarios

> **Purpose:** Ready-to-use demo scripts for showcasing MCP capabilities to different audiences

## 🎯 How to Use This Guide

1. **Choose Your Audience:** Select the scenario matching your audience type
2. **Practice First:** Run through the demo at least twice before presenting
3. **Customize:** Replace sample data with industry-specific examples when possible
4. **Time Box:** Each scenario is designed for 3-5 minutes
5. **Be Conversational:** Don't just read questions—explain the business context

---

## 📊 Scenario 1: Executive Dashboard (C-Suite)

**Audience:** CEO, CFO, COO, Board Members
**Duration:** 4 minutes
**Goal:** Show real-time business intelligence access

### Setup
- Open Claude Desktop
- Have quarterly date ranges ready (e.g., "Q4 2024" = Oct-Dec)
- Screen share entire desktop

### Script

**[Opening - 15 seconds]**
*"Imagine you're heading into a board meeting in 5 minutes and need last-minute insights. Traditionally, you'd email your data team and hope for a response. Let me show you a different approach..."*

**[Question 1 - Revenue Overview]**
```
"Claude, what was our total revenue for the last quarter?"
```
**Expected Response:** Total revenue figure with period details

**[Narration]:** *"Notice the instant response—this is pulling from our live database. No pre-built dashboard needed."*

**[Question 2 - Trend Analysis]**
```
"Show me revenue by month for the past 3 months. Is it growing?"
```
**Expected Response:** Monthly breakdown with growth analysis

**[Narration]:** *"Now I'm getting trend analysis. The AI understands context—it knows I care about growth direction, not just numbers."*

**[Question 3 - Product Performance]**
```
"Which 5 products generated the most revenue?"
```
**Expected Response:** Top 5 products with revenue figures

**[Narration]:** *"Here's where it gets powerful—I can drill down without building new reports."*

**[Question 4 - Customer Insights]**
```
"What's our average order value and how many unique customers did we have?"
```
**Expected Response:** AOV and customer count metrics

**[Narration]:** *"These are the KPIs you'd typically have on a dashboard—but I can ask for ANY metric, not just what was pre-configured."*

**[Closing - 30 seconds]**
*"That's 4 complex business questions answered in under 2 minutes. With traditional BI, each of these might require a separate dashboard or report request. This is how modern executives should access their data—conversationally, instantly, and comprehensively."*

### Objection Pre-emption
If they ask: *"But what about security?"*
👉 *"Great question! The MCP server runs entirely within your infrastructure. Claude never sees your database directly—only the specific answers we authorize it to receive, just like any API."*

---

## 🎧 Scenario 2: Customer Service Excellence

**Audience:** Customer Service Managers, Support Directors
**Duration:** 3 minutes
**Goal:** Demonstrate 360° customer view for support agents

### Setup
- Have a sample customer ID ready (e.g., customer #42)
- Consider using a split screen showing a mock support ticket

### Script

**[Opening - 15 seconds]**
*"Your support agent has a customer on the line who's upset about a delivery. Instead of putting them on hold while navigating 3 different systems, watch this..."*

**[Question 1 - Customer Overview]**
```
"Show me the order history for customer #42"
```
**Expected Response:** List of all orders with dates, amounts, statuses

**[Narration]:** *"In 2 seconds, the agent has complete order history. No system switching, no hold time."*

**[Question 2 - Customer Value]**
```
"What's this customer's lifetime value and how long have they been a customer?"
```
**Expected Response:** Total spend, first order date, order frequency

**[Narration]:** *"Now the agent knows this is a high-value customer who deserves priority treatment. That context matters for service quality."*

**[Question 3 - Payment Status]**
```
"Are there any overdue invoices for this customer?"
```
**Expected Response:** List of overdue invoices or "No overdue invoices"

**[Narration]:** *"The agent can proactively address payment issues during the call, turning a complaint into a complete account review."*

**[Question 4 - Purchase Patterns]**
```
"What products does this customer buy most frequently?"
```
**Expected Response:** Product categories or specific items

**[Narration]:** *"And now the agent can offer personalized recommendations or acknowledge their preferences—building relationship, not just resolving tickets."*

**[Closing - 20 seconds]**
*"That entire 360° customer view took 30 seconds. Industry average hold time is 5-10 minutes just to gather this info. Multiply that across hundreds of daily support calls—you're looking at 3x productivity improvement and dramatically better customer satisfaction."*

### ROI Calculation (Show on Screen)
```
Current State:
- Average hold time: 7 minutes per call
- Support calls per day: 100
- Annual cost: 700 min/day × 260 days × agent hourly rate

With MCP:
- Average hold time: 0 minutes
- Same quality of service, 3x more calls handled
- Annual savings: [Calculate based on their agent costs]
```

---

## 📦 Scenario 3: Inventory & Operations

**Audience:** Operations Managers, Supply Chain Directors, Warehouse Managers
**Duration:** 3 minutes
**Goal:** Show proactive inventory management

### Setup
- Know your low-stock threshold (e.g., <10 units)
- Have a product search ready (e.g., "laptop")

### Script

**[Opening - 10 seconds]**
*"It's Monday morning. You need to prioritize today's procurement. Let's see what needs attention..."*

**[Question 1 - Inventory Alerts]**
```
"What inventory alerts do I have right now?"
```
**Expected Response:** Low stock items, out of stock items, overdue invoices

**[Narration]:** *"Instant alert summary—critically low stock, completely out of stock items, and even overdue invoices that might impact cash flow."*

**[Question 2 - Product Search]**
```
"Show me all products containing 'laptop' with their current stock levels"
```
**Expected Response:** Laptop products with inventory quantities

**[Narration]:** *"Now I can search specific product categories. This is like having a warehouse assistant who knows every SKU instantly."*

**[Question 3 - Sales Velocity Context]**
```
"Which low-stock products are also our top sellers?"
```
**Expected Response:** Cross-reference of low stock items with sales data

**[Narration]:** *"Here's the power move—combining inventory data with sales analytics to prioritize what actually matters. A low-stock item that rarely sells? Low priority. A low-stock bestseller? Order now."*

**[Question 4 - Revenue Impact]**
```
"If we stock out on our top 5 products, what's the potential revenue loss?"
```
**Expected Response:** Revenue impact calculation

**[Narration]:** *"Now I can make a business case to procurement or finance—'Here's the cost of NOT restocking urgently.'"*

**[Closing - 20 seconds]**
*"Traditional inventory management: Export CSV, open Excel, manually cross-reference sales reports, email procurement team—2-3 hours. With MCP: 4 questions in 90 seconds, and you're making informed prioritization decisions. This is how modern supply chains should operate."*

---

## 💰 Scenario 4: Financial Analysis (Finance Teams)

**Audience:** CFO, Finance Directors, Financial Analysts
**Duration:** 4 minutes
**Goal:** Demonstrate ad-hoc financial reporting

### Setup
- Have recent date ranges ready (last 30/60/90 days)
- Know sample invoice IDs

### Script

**[Opening - 15 seconds]**
*"Quarter end is approaching, and you need to understand revenue patterns to forecast next quarter. Instead of waiting for monthly reports, you can explore interactively..."*

**[Question 1 - Revenue Trends]**
```
"Show me daily revenue for the past 60 days"
```
**Expected Response:** Day-by-day revenue breakdown

**[Narration]:** *"Granular daily data—this would normally require a custom BI report build."*

**[Question 2 - Pattern Recognition]**
```
"Are there any unusual spikes or drops in that data?"
```
**Expected Response:** AI analysis of anomalies

**[Narration]:** *"The AI is doing anomaly detection for us—highlighting patterns a human might miss in spreadsheets."*

**[Question 3 - Revenue Breakdown]**
```
"What's the revenue breakdown by product category for last month?"
```
**Expected Response:** Category-wise revenue split

**[Narration]:** *"Now we're drilling into revenue composition—understanding where money comes from."*

**[Question 4 - Customer Concentration Risk]**
```
"Show me our top 10 customers by total spend. What percentage of revenue do they represent?"
```
**Expected Response:** Top customers with concentration percentage

**[Narration]:** *"Critical risk analysis—if top 3 customers are 50%+ of revenue, that's a concentration risk for investors and lenders to know about."*

**[Question 5 - Cash Flow]**
```
"How many invoices are overdue and what's the total outstanding amount?"
```
**Expected Response:** Overdue count and total receivables

**[Narration]:** *"Cash flow monitoring—overdue invoices directly impact working capital. This should be reviewed daily, not monthly."*

**[Closing - 30 seconds]**
*"These 5 questions represent hours of analyst work or waiting for monthly report cycles. For financial planning and forecasting, you need this agility—the ability to ask follow-up questions, explore hypotheses, and validate assumptions in real-time. That's the difference between reactive finance and strategic finance."*

---

## 🔍 Scenario 5: Technical Deep-Dive (IT/Engineering)

**Audience:** CTO, CIO, Engineering Managers, DevOps Teams
**Duration:** 4 minutes
**Goal:** Show system monitoring and technical capabilities

### Setup
- Be ready to show MCP Inspector interface
- Have architecture diagram available

### Script

**[Opening - 10 seconds]**
*"Let me show you both the user-facing capabilities and the technical architecture behind it..."*

**[Question 1 - System Health]**
```
"Run a health check on the application"
```
**Expected Response:** System status, uptime, key metrics

**[Narration]:** *"Basic health monitoring—but notice this is conversational. I could ask follow-ups like 'What was uptime last week?' without pre-configuring dashboards."*

**[Question 2 - System Information]**
```
"What version of Laravel and PHP are we running?"
```
**Expected Response:** Framework and runtime versions

**[Narration]:** *"Application metadata—useful for security compliance and dependency audits."*

**[Question 3 - Database Inspection]**
```
"Show me database table information and row counts"
```
**Expected Response:** List of tables with row counts

**[Narration]:** *"Database observability without needing to SSH into servers or run SQL queries."*

**[Switch to MCP Inspector - 1 minute]**
*"Now let me show you how this works under the hood..."*

- Open http://localhost:6274 (MCP Inspector)
- Navigate through tool list
- Show a tool schema (e.g., `get_sales_stats`)
- Demonstrate testing a tool with custom parameters

**[Narration during Inspector demo]:**
*"The MCP Inspector lets developers test tools before deploying. Each tool has a schema that defines parameters, validation rules, and expected outputs. The LLM reads these schemas to understand how to use the tools—it's self-documenting."*

**[Architecture Discussion - 1 minute]**
*Show architecture diagram (from README)*

- **Point to MCP Layer:** "This is where AI requests come in—validated and authenticated"
- **Point to Service Layer:** "Business logic lives here—completely testable independent of MCP"
- **Point to Database:** "Your data never leaves your infrastructure"

**[Closing - 30 seconds]**
*"From an engineering perspective, this is clean architecture—separation of concerns, dependency injection, testability. We're not hacking AI onto your codebase; we're exposing existing business logic through a conversational interface. The MCP protocol is open-source and Anthropic-backed, so you're building on a foundation with staying power."*

### Technical Objection Handling

**"What about rate limiting?"**
👉 *"Built into Laravel—you can apply standard throttling middleware to MCP endpoints."*

**"How do we handle errors?"**
👉 *"Schema validation catches malformed requests before they hit your services. Runtime errors return structured error responses that the AI can interpret and explain to users."*

**"Can we add custom tools?"**
👉 *"Absolutely. Run `php artisan make:mcp-tool YourTool`, implement the schema and handler, register it—done. Takes 10-30 minutes for a simple tool."*

---

## 🎓 Scenario 6: Research & Innovation (Academic/Grant Reviewers)

**Audience:** Grant reviewers, academic partners, research institutions
**Duration:** 5 minutes
**Goal:** Emphasize novel research contributions and methodology

### Setup
- Have BUSINESS_CASE.md open in another tab
- Prepare to discuss specific research questions

### Script

**[Opening - 20 seconds]**
*"This isn't just a commercial product—it's a research platform exploring fundamental questions about how AI systems should interact with enterprise data. Let me demonstrate current capabilities, then discuss the research agenda..."*

**[Demo - 2 minutes]**
Run abbreviated version of Scenario 1 (Executive Dashboard) to show baseline functionality

**[Transition to Research Discussion - 2.5 minutes]**

**Research Question 1: Security & Authorization**
*"How do we implement fine-grained access control for conversational AI?"*
- Traditional systems: User clicks button → Check permission
- Conversational AI: User asks question → How do we know what data they should see?
- **Our Approach:** Schema-level permissions + service-layer RBAC
- **Open Question:** Can we infer authorization from conversational context?

**Research Question 2: Context & State Management**
*"LLMs are stateless, but business conversations are stateful. How do we bridge that gap?"*
- Example: "Show me revenue" → "Now break that down by product"
  - What does "that" refer to?
  - What time period was implied?
- **Our Approach:** Schema design encourages explicit parameters
- **Open Question:** Can we build conversational state management into MCP?

**Research Question 3: Performance & Caching**
*"Traditional caching assumes identical queries. AI queries are never identical."*
- "Revenue last month" vs "Show me revenue for November 2024" → Same data, different phrasing
- **Our Approach:** Semantic query analysis for cache key generation
- **Open Question:** Can we build LLM-aware caching layers?

**[Publication & Impact Plan - 30 seconds]**
*"We're targeting 2 academic publications—likely ACM SIGMOD and IEEE—plus 3 industry whitepapers. The goal is to contribute learnings back to both the MCP specification and the broader research community exploring AI-database interaction patterns."*

**[Closing - 20 seconds]**
*"This represents applied research with real-world validation—not just theoretical models. By Month 18, we'll have 50+ production deployments generating quantitative data on query patterns, performance characteristics, and user behavior. That's a research dataset most academic labs can't access."*

---

## 🛠️ Demo Environment Setup Checklist

### Before Every Demo:

- [ ] Docker containers running: `./vendor/bin/sail ps`
- [ ] Database seeded with sample data: `./vendor/bin/sail artisan migrate:fresh --seed`
- [ ] Claude Desktop connected and MCP tools visible
- [ ] Screen sharing tested (check audio/video)
- [ ] Browser tabs prepared:
  - [ ] Claude Desktop
  - [ ] MCP Inspector (http://localhost:6274)
  - [ ] Architecture diagram (README.md)
  - [ ] Backup: screen recording of demo in case of technical issues
- [ ] Note recent data points (revenue totals, product counts) to verify answers
- [ ] Close unnecessary applications (email, Slack) to minimize distractions

### Test Questions (Run 5 minutes before demo):
```
"What was our total revenue last quarter?"
"Show me top 5 products by revenue"
"What inventory alerts do I have?"
"Run a health check"
```
If any fail → Troubleshoot before starting demo

---

## 📊 Customization Guide

### Adapting for Different Industries:

**Retail/E-commerce (default):**
- Use as-is with product, order, inventory terminology

**SaaS/Software:**
- Replace "products" with "subscription plans"
- Replace "orders" with "signups" or "conversions"
- Replace "inventory" with "user limits" or "API quotas"

**Manufacturing:**
- Replace "products" with "SKUs" or "parts"
- Replace "orders" with "production runs"
- Replace "inventory" with "raw materials" or "WIP"

**Healthcare:**
- Replace "customers" with "patients"
- Replace "orders" with "appointments" or "treatments"
- Replace "revenue" with "billing" or "reimbursements"
- **Note:** Add HIPAA compliance discussion for this vertical

**Financial Services:**
- Replace "products" with "accounts" or "portfolios"
- Replace "orders" with "transactions"
- Replace "customers" with "clients" or "account holders"
- **Note:** Add regulatory compliance (SOX, PCI) discussion

### Creating Custom Demo Data:

```bash
# Edit database/seeders/DatabaseSeeder.php to change:
- Company name
- Product categories
- Revenue amounts
- Date ranges

# Reseed:
./vendor/bin/sail artisan migrate:fresh --seed

# Verify:
./vendor/bin/sail artisan tinker
>>> \App\Models\Order::sum('total_amount')
```

---

## 🎬 Post-Demo Actions

### Immediate Follow-Up (Within 24 hours):

1. **Send Thank You Email** with:
   - Link to GitHub repository
   - PDF of architecture diagram
   - Relevant case study (industry-matched if possible)
   - Calendar link for technical deep-dive or POC discussion

2. **Update CRM** with:
   - Demo date and attendees
   - Questions asked and objections raised
   - Perceived interest level (1-5 scale)
   - Next action items and timeline

### Qualify for Next Steps:

**Hot Lead (proceed to POC):**
- Asked about pricing or timeline
- Requested technical deep-dive
- Mentioned specific use cases or data sources
- Introduced additional stakeholders

**Warm Lead (nurture):**
- Engaged but needs internal discussion
- Budget or timing unclear
- Competing priorities
- Needs executive buy-in

**Cold Lead (long-term nurture):**
- Polite interest but no urgency
- Satisfied with current solution
- No budget identified
- Just exploring/learning

---

## 📚 Additional Resources to Reference During Demos

### When They Ask About MCP Protocol:
👉 https://modelcontextprotocol.io

### When They Ask About Laravel:
👉 https://laravel.com

### When They Ask About Security Best Practices:
👉 Refer to BUSINESS_CASE.md Security section

### When They Ask About Implementation Timeline:
👉 "Typical Laravel integration: 2-4 weeks from kickoff to production"

### When They Ask About Support:
👉 "We offer tiered support: Community (GitHub), Professional (email SLA), Enterprise (dedicated Slack channel + phone)"

---

*This demo scenarios guide should be practiced regularly and updated based on field feedback. Record demos (with permission) to review and improve delivery. The best sales demos feel conversational, not scripted—but having a script to fall back on prevents awkward pauses.*

**Document Version:** 1.0
**Last Updated:** 2025-01
**Maintained By:** Sales Enablement Team
