# Monitor Dashboard Filter Feature

## Overview
The monitor dashboard now supports filtering data by individual model or query, allowing you to drill down into specific performance metrics.

## Features Added

### 1. Filter Controls
- **Location**: Between the header and run info sections
- **Two dropdown filters**:
  - Filter by Model: Shows all active models
  - Filter by Query: Shows all queries that have been tested
- **Apply/Clear buttons**: 
  - Apply: Submit the selected filters
  - Clear: Remove all active filters (only shown when filters are active)

### 2. Active Filter Display
When filters are applied, a visual indicator shows which filters are active with colored badges.

### 3. Clickable Elements
All the following elements are now clickable to quickly filter:
- **Model names** in the Model Performance Table → Filters by that model
- **Model names** in Recent Citations → Filters by that model  
- **Queries** in Recent Citations → Filters by that query
- **Queries** in the Query Breakdown Table (when filtering by model) → Adds query filter

Hover over clickable elements to see the hover effect (green glow).

### 4. Query Breakdown Table
When filtering by a specific model (without a query filter), a new table appears showing:
- All queries tested with that model
- Number of times each query was tested
- Number of citations for each query
- Citation rate per query
- Queries are clickable to further filter

### 5. Dynamic Data Updates
All sections update based on active filters:
- **Statistics cards**: Update subtext to reflect what's being measured
- **Model Performance Chart**: Shows data for filtered results
- **Model Performance Table**: Title changes when filtering by query
- **Timeline Chart**: Title shows active filters
- **Recent Citations**: Filtered to match selected criteria

## Usage Examples

### View Performance for a Specific Model
1. Select model from the "Filter by Model" dropdown
2. Click "Apply"
3. See: Overall stats for that model, all queries tested with it, citations over time

### View Performance for a Specific Query Across All Models
1. Select query from the "Filter by Query" dropdown  
2. Click "Apply"
3. See: Which models performed best with that query, citations over time

### View a Specific Model + Query Combination
1. Select both model and query
2. Click "Apply"
3. See: Detailed performance for that specific combination

### Quick Filtering via Clicks
- Click any model name in tables to filter by that model
- Click any query in tables to filter by that query
- Filters stack (clicking a model then a query applies both)

## Visual Design
- Maintains the terminal/CRT aesthetic
- Cyan (#00d9ff) for primary UI elements
- Green (#00ff88) for success/active states and clickable hover effects
- Orange (#ff8800) for the clear button
- Smooth transitions and hover effects on all interactive elements

## Technical Details
- Uses GET parameters (`?model=...&query=...`)
- All SQL queries properly parameterized for security
- Filters are preserved when clicking between related items
- Charts automatically adjust to filtered data

