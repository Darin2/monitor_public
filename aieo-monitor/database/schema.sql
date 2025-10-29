-- AI Citation Monitor - MySQL Schema
-- For use with Bluehost MySQL database

-- Queries table: Store all test queries
CREATE TABLE IF NOT EXISTS queries (
    id VARCHAR(50) PRIMARY KEY,
    query_text TEXT NOT NULL,
    category VARCHAR(100),
    priority INT DEFAULT 1,
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Models table: Track which AI models we're testing
CREATE TABLE IF NOT EXISTS models (
    id VARCHAR(50) PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    provider VARCHAR(50) NOT NULL,
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Runs table: Track each execution of the monitor (weekly cron runs)
CREATE TABLE IF NOT EXISTS runs (
    run_id VARCHAR(50) PRIMARY KEY,
    started_at TIMESTAMP NOT NULL,
    completed_at TIMESTAMP NULL,
    status VARCHAR(20) NOT NULL,
    queries_executed INT DEFAULT 0,
    errors_count INT DEFAULT 0,
    notes TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Responses table: Store all query results
CREATE TABLE IF NOT EXISTS responses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    run_id VARCHAR(50) NOT NULL,
    timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    query_id VARCHAR(50) NOT NULL,
    model_id VARCHAR(50) NOT NULL,
    query_text TEXT NOT NULL,
    response TEXT NOT NULL,
    paintballevents_referenced BOOLEAN NOT NULL DEFAULT FALSE,
    search_query TEXT,
    cited_urls JSON,
    response_time_ms INT,
    error TEXT,
    INDEX idx_run_id (run_id),
    INDEX idx_query_id (query_id),
    INDEX idx_model_id (model_id),
    INDEX idx_timestamp (timestamp),
    INDEX idx_paintballevents (paintballevents_referenced),
    FOREIGN KEY (run_id) REFERENCES runs(run_id) ON DELETE CASCADE,
    FOREIGN KEY (query_id) REFERENCES queries(id) ON DELETE CASCADE,
    FOREIGN KEY (model_id) REFERENCES models(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default models
INSERT INTO models (id, name, provider, active) VALUES
('gpt-5', 'GPT-5', 'OpenAI', TRUE),
('gpt-5-mini', 'GPT-5-mini', 'OpenAI', TRUE),
('gpt-5-nano', 'GPT-5-nano', 'OpenAI', TRUE),
('claude-3-7-sonnet', 'Claude 3.7 Sonnet', 'Anthropic', TRUE),
('claude-sonnet-4-5', 'Claude Sonnet 4.5', 'Anthropic', TRUE),
('claude-haiku-4-5', 'Claude Haiku 4.5', 'Anthropic', TRUE),
('claude-opus-4-1', 'Claude Opus 4.1', 'Anthropic', TRUE),
('deepseek-chat', 'DeepSeek Chat', 'DeepSeek', FALSE),
('grok-2', 'Grok 2', 'xAI', FALSE),
('sonar-pro', 'Sonar Pro', 'Perplexity', FALSE),
('llama-3-70b', 'Llama 3 70B', 'Meta', FALSE)
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- View for easy querying: Model performance summary
CREATE OR REPLACE VIEW model_performance AS
SELECT 
    m.id as model_id,
    m.name as model_name,
    m.provider,
    COUNT(r.id) as total_queries,
    SUM(r.paintballevents_referenced) as times_cited,
    ROUND(SUM(r.paintballevents_referenced) / COUNT(r.id) * 100, 1) as citation_rate,
    AVG(r.response_time_ms) as avg_response_time_ms,
    MAX(r.timestamp) as last_tested
FROM models m
LEFT JOIN responses r ON m.id = r.model_id
WHERE m.active = TRUE
GROUP BY m.id, m.name, m.provider;

-- View for easy querying: Query performance summary
CREATE OR REPLACE VIEW query_performance AS
SELECT 
    q.id as query_id,
    q.query_text,
    q.category,
    COUNT(r.id) as times_tested,
    SUM(r.paintballevents_referenced) as times_cited,
    ROUND(SUM(r.paintballevents_referenced) / COUNT(r.id) * 100, 1) as citation_rate,
    MAX(r.timestamp) as last_tested
FROM queries q
LEFT JOIN responses r ON q.id = r.query_id
WHERE q.active = TRUE
GROUP BY q.id, q.query_text, q.category;

-- View for dashboard: Recent citations
CREATE OR REPLACE VIEW recent_citations AS
SELECT 
    r.id,
    r.timestamp,
    r.run_id,
    m.name as model_name,
    q.query_text,
    r.cited_urls,
    r.search_query
FROM responses r
JOIN models m ON r.model_id = m.id
JOIN queries q ON r.query_id = q.id
WHERE r.paintballevents_referenced = TRUE
ORDER BY r.timestamp DESC
LIMIT 50;

