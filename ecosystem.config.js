module.exports = {
    apps: [{
        name: "HCD Server",
        script: "./server.js",
        node_args: '-r dotenv/config dotenv_config_path=.env.local',
        env_local: {
            NODE_ENV: "local",
        }
    }]
}