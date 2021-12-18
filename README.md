# General
**RegionProtect is temporarily called a protected region?**
Once the designated region is protected, the player will no longer worry about the features in a world, and when entering that region, it will display the title to let the player know the characteristics of the region there.

[![State](https://poggit.pmmp.io/shield.state/RegionProtect)](https://poggit.pmmp.io/p/RegionProtect)
[![API](https://poggit.pmmp.io/shield.api/RegionProtect)](https://poggit.pmmp.io/p/RegionProtect)
[![Downloads Total](https://poggit.pmmp.io/shield.dl.total/RegionProtect)](https://poggit.pmmp.io/p/RegionProtect)
[![Downloads](https://poggit.pmmp.io/shield.dl/RegionProtect)](https://poggit.pmmp.io/p/RegionProtect)
[![Lint](https://poggit.pmmp.io/ci.shield/kaidoMC-pm-pl/RegionProtect/RegionProtect)](https://poggit.pmmp.io/ci/kaidoMC-pm-pl/RegionProtect/RegionProtect)

# Features
- Show title when entering region.
- Allow or disallow pvp in the region.
- Allow or disallow block breaking in the region.
- Allow or disallow placing in the region.
- Allow or disallow touching (interaction) in the region.

# How to use?
1. `/region wand` - Take the wooden ax and choose the vector.
2. Select the start and end points to set up the frameset for the region.
3. `/region create` - You are required to fill in information to create an region.
4.  `/region edit <regionName>` - Edit the region the you wants.

# Permissions
| Permission               | Description                                                                       |
| ------------------------ | --------------------------------------------------------------------------------- |
| `region.command.use`     | Basic permission to be able to use the Region feature.                            |
| `region.interactive.use` | Permission is allowed to interact in the forbidden region. `(Developing)`         |

# Futures
- [ ] Notification type: subtitle, mess, popup, tip.
- [ ] Sound.
- [ ] Etc.

# Configs
```yaml
---
# If true, show title when player enters an region.
show-title: true

# Warning message are sent to players when their actions are blocked.
warning-message: "Can't do this in the standing region."

# Timeout between warning message in seconds.
waiting-message: 3

# Operator players can still act in the forbidden region.
interactive-operator: true
...
```

# Contacts
**You can contact me directly through the platforms listed below**
- Discord: @kaidojoestar272004#7124
- Facebook: https://www.fb.com/kaidomc12345
- Youtube: https://www.youtube.com/KAIDOMC
# License
[**GNU General Public License v3.0**](https://www.gnu.org/licenses/gpl-3.0.html)
