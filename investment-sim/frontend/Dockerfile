# Use Node.js as the base image
FROM node:18

# Set the working directory inside the container
WORKDIR /app

# Copy package.json and install dependencies
COPY package*.json ./
RUN npm install

# Copy all frontend files into the container
COPY . .

# Expose the Vite development server port
EXPOSE 5173

# Start Vite with proper host binding
CMD ["npm", "run", "dev", "--", "--host"]
