-- Migration: Add Claude 4.5 models (Sonnet 4.5, Haiku 4.5, Opus 4.1)
-- Date: 2025-10-29
-- Description: Adds support for the new Claude 4.5 model family

-- Add the new Claude 4.5 models
INSERT INTO models (id, name, provider, active) VALUES
('claude-sonnet-4-5', 'Claude Sonnet 4.5', 'Anthropic', TRUE),
('claude-haiku-4-5', 'Claude Haiku 4.5', 'Anthropic', TRUE),
('claude-opus-4-1', 'Claude Opus 4.1', 'Anthropic', TRUE)
ON DUPLICATE KEY UPDATE name=VALUES(name), provider=VALUES(provider);

-- Verify the new models were added
SELECT id, name, provider, active FROM models WHERE provider = 'Anthropic';

