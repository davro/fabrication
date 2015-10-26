(GCode fabricated on Mon, 26 Oct 2015 20:28:06 +0000 http://davro.net)
G90 (absolute mode)
F1000  (Feed Rate)
S0  (Spindle Speed)
G21 (Metric mm)

(circle x=50 y=50 z=0 radius=30 plane=G17 ofx=20 ofy=20)
G0 X20 Y50 (rapid start)
G1 Z0
G17 G2 X20 Y50 I30 J0.00 Z0 (Plane)
G0 Z10 (move z)
(/circle)

(circle x=120 y=50 z=0 radius=30 plane=G17 ofx=90 ofy=20)
G0 X90 Y50 (rapid start)
G1 Z0
G17 G2 X90 Y50 I30 J0.00 Z0 (Plane)
G0 Z10 (move z)
(/circle)

(circle x=190 y=50 z=0 radius=30 plane=G17 ofx=160 ofy=20)
G0 X160 Y50 (rapid start)
G1 Z0
G17 G2 X160 Y50 I30 J0.00 Z0 (Plane)
G0 Z10 (move z)
(/circle)

(circle x=260 y=50 z=0 radius=30 plane=G17 ofx=230 ofy=20)
G0 X230 Y50 (rapid start)
G1 Z0
G17 G2 X230 Y50 I30 J0.00 Z0 (Plane)
G0 Z10 (move z)
(/circle)

(circle x=50 y=120 z=0 radius=30 plane=G17 ofx=20 ofy=90)
G0 X20 Y120 (rapid start)
G1 Z0
G17 G2 X20 Y120 I30 J0.00 Z0 (Plane)
G0 Z10 (move z)
(/circle)

(circle x=120 y=120 z=0 radius=30 plane=G17 ofx=90 ofy=90)
G0 X90 Y120 (rapid start)
G1 Z0
G17 G2 X90 Y120 I30 J0.00 Z0 (Plane)
G0 Z10 (move z)
(/circle)

(circle x=190 y=120 z=0 radius=30 plane=G17 ofx=160 ofy=90)
G0 X160 Y120 (rapid start)
G1 Z0
G17 G2 X160 Y120 I30 J0.00 Z0 (Plane)
G0 Z10 (move z)
(/circle)

(circle x=260 y=120 z=0 radius=30 plane=G17 ofx=230 ofy=90)
G0 X230 Y120 (rapid start)
G1 Z0
G17 G2 X230 Y120 I30 J0.00 Z0 (Plane)
G0 Z10 (move z)
(/circle)
M2
