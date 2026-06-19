# Wedding Card Audio Files

This directory contains audio files used in wedding invitation templates with background music player functionality.

## File Structure
- `perfect.mp3` - Default song 1 (Ed Sheeran - Perfect)
- `all-of-me.mp3` - Default song 2 (John Legend - All of Me)
- `thinking-out-loud.mp3` - Default song 3 (Ed Sheeran - Thinking Out Loud)
- `thousand-years.mp3` - Default song 4 (Christina Perri - A Thousand Years)
- `marry-me.mp3` - Default song 5 (Train - Marry Me)

## Audio Requirements
- **Format**: MP3, WAV, OGG, AAC, M4A
- **File Size**: Maximum 10MB per file
- **Duration**: Recommended 3-5 minutes for background music
- **Quality**: 128-320 kbps for optimal loading balance

## Template Integration
Songs are referenced in templates using variables:
- `{{ $details['song_1_url'] ?? '/audio/perfect.mp3' }}`
- `{{ $details['song_2_url'] ?? '/audio/all-of-me.mp3' }}`
- etc.

## Admin Upload
Admins can upload custom audio files through:
1. Template creation/editing interface
2. "Parse Variables from Template" button automatically detects `song_*_url` variables
3. File upload fields created automatically for audio variables
4. Preview player available for uploaded audio

## Music Player Features
- Auto-play with fade-in effect
- Play/pause controls
- Volume control
- Next/previous track navigation
- Progress bar with seek functionality
- Keyboard shortcuts (Space = play/pause, Arrow keys = skip)
- Mobile responsive design
- Minimizable player widget

## Copyright Notice
⚠️ **Important**: Ensure you have proper licensing for any music used in commercial wedding invitations. Consider using:
- Royalty-free music
- Creative Commons licensed tracks
- Original compositions
- Licensed music from platforms like AudioJungle, Epidemic Sound, etc.

## Default Fallbacks
If custom songs are not provided, the template will attempt to load default files from this directory. These are fallback references only - actual files need to be added separately due to copyright restrictions. 