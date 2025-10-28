-- Migration Script: GPT-4o to GPT-5 Models
-- This script updates the database to use GPT-5, GPT-5-mini, and GPT-5-nano instead of GPT-4o

-- Step 1: Add new GPT-5 models
INSERT INTO models (id, name, provider, active) VALUES
('gpt-5', 'GPT-5', 'OpenAI', TRUE),
('gpt-5-mini', 'GPT-5-mini', 'OpenAI', TRUE),
('gpt-5-nano', 'GPT-5-nano', 'OpenAI', TRUE)
ON DUPLICATE KEY UPDATE name=VALUES(name), active=VALUES(active);

-- Step 2: Deactivate GPT-4o model (preserve historical data)
UPDATE models SET active = FALSE WHERE id = 'gpt-4o';

-- Step 3: Optional - Update historical GPT-4o responses to GPT-5
-- Uncomment the following if you want to migrate existing gpt-4o data to gpt-5
-- WARNING: This is irreversible!

-- UPDATE responses SET model_id = 'gpt-5' WHERE model_id = 'gpt-4o';

-- Step 4: Verify migration
SELECT id, name, provider, active FROM models WHERE provider = 'OpenAI' ORDER BY active DESC, id;

