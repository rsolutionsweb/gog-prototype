# Use the official Playwright image with browsers pre-installed
FROM mcr.microsoft.com/playwright:v1.50.0-jammy

# Set working directory
WORKDIR /app

# Copy package files
COPY package*.json ./

# Install dependencies
RUN npm ci

# Copy test files and configuration
COPY . .

# Run tests
CMD ["npx", "playwright", "test"]