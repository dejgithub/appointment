import { Button } from "@/components/ui/button"
import { Card, CardContent } from "@/components/ui/card"
import { Badge } from "@/components/ui/badge"
import {
  Calendar,
  Clock,
  Stethoscope,
  Heart,
  Brain,
  Bone,
  Baby,
  Microscope,
  Phone,
  Mail,
  MapPin,
  Star,
  CheckCircle,
  Users,
  Award,
  Shield,
} from "lucide-react"
import Link from "next/link"
import Image from "next/image"

export default function HospitalHomePage() {
  const departments = [
    { name: "Cardiology", icon: Heart, description: "Heart & Cardiovascular Care", color: "bg-red-100 text-red-700" },
    { name: "Neurology", icon: Brain, description: "Brain & Nervous System", color: "bg-purple-100 text-purple-700" },
    { name: "Orthopedics", icon: Bone, description: "Bone & Joint Care", color: "bg-blue-100 text-blue-700" },
    { name: "Pediatrics", icon: Baby, description: "Children's Healthcare", color: "bg-green-100 text-green-700" },
    {
      name: "Dermatology",
      icon: Microscope,
      description: "Skin & Beauty Care",
      color: "bg-yellow-100 text-yellow-700",
    },
    {
      name: "General Medicine",
      icon: Stethoscope,
      description: "General Healthcare",
      color: "bg-gray-100 text-gray-700",
    },
  ]

  const doctors = [
    {
      name: "Dr. Sarah Johnson",
      specialization: "Cardiologist",
      experience: "15 years",
      rating: 4.9,
      image: "/placeholder.svg?height=200&width=200&text=Dr.+Sarah+Johnson",
    },
    {
      name: "Dr. Michael Chen",
      specialization: "Neurologist",
      experience: "12 years",
      rating: 4.8,
      image: "/placeholder.svg?height=200&width=200&text=Dr.+Michael+Chen",
    },
    {
      name: "Dr. Emily Davis",
      specialization: "Orthopedic Surgeon",
      experience: "10 years",
      rating: 4.9,
      image: "/placeholder.svg?height=200&width=200&text=Dr.+Emily+Davis",
    },
  ]

  const stats = [
    { number: "50+", label: "Expert Doctors", icon: Users },
    { number: "15+", label: "Years of Excellence", icon: Award },
    { number: "10000+", label: "Happy Patients", icon: Heart },
    { number: "24/7", label: "Emergency Care", icon: Shield },
  ]

  return (
    <div className="min-h-screen bg-gradient-to-b from-blue-50 to-white">
      {/* Header */}
      <header className="bg-white shadow-md sticky top-0 z-50">
        <div className="container mx-auto px-4 py-4">
          <div className="flex items-center justify-between">
            <div className="flex items-center space-x-2">
              <div className="bg-blue-600 p-2 rounded-lg">
                <Stethoscope className="h-8 w-8 text-white" />
              </div>
              <div>
                <h1 className="text-2xl font-bold text-blue-900">HealthCare Plus</h1>
                <p className="text-sm text-gray-600">Your Health, Our Priority</p>
              </div>
            </div>
            <nav className="hidden md:flex space-x-6">
              <Link href="#home" className="text-gray-700 hover:text-blue-600 font-medium">
                Home
              </Link>
              <Link href="#departments" className="text-gray-700 hover:text-blue-600 font-medium">
                Departments
              </Link>
              <Link href="#doctors" className="text-gray-700 hover:text-blue-600 font-medium">
                Doctors
              </Link>
              <Link href="#contact" className="text-gray-700 hover:text-blue-600 font-medium">
                Contact
              </Link>
            </nav>
            <div className="flex space-x-2">
              <Button variant="outline">Login</Button>
              <Button className="bg-blue-600 hover:bg-blue-700">Book Appointment</Button>
            </div>
          </div>
        </div>
      </header>

      {/* Hero Section */}
      <section id="home" className="relative py-20 bg-gradient-to-r from-blue-600 to-blue-800 text-white">
        <div className="container mx-auto px-4">
          <div className="grid lg:grid-cols-2 gap-12 items-center">
            <div>
              <h2 className="text-5xl font-bold mb-6">
                Your Health is Our
                <span className="text-yellow-400"> Priority</span>
              </h2>
              <p className="text-xl mb-8 text-blue-100">
                Experience world-class healthcare with our team of expert doctors and state-of-the-art facilities. Book
                your appointment today and take the first step towards a healthier you.
              </p>
              <div className="flex flex-col sm:flex-row gap-4">
                <Button size="lg" className="bg-yellow-500 hover:bg-yellow-600 text-black font-semibold">
                  <Calendar className="mr-2 h-5 w-5" />
                  Book Appointment
                </Button>
                <Button
                  size="lg"
                  variant="outline"
                  className="border-white text-white hover:bg-white hover:text-blue-600 bg-transparent"
                >
                  <Phone className="mr-2 h-5 w-5" />
                  Emergency: 911
                </Button>
              </div>
            </div>
            <div className="relative">
              <Image
                src="/placeholder.svg?height=500&width=600&text=Modern+Hospital+Building"
                alt="Hospital"
                width={600}
                height={500}
                className="rounded-lg shadow-2xl"
              />
              <div className="absolute -bottom-6 -left-6 bg-white text-blue-600 p-4 rounded-lg shadow-lg">
                <div className="flex items-center space-x-2">
                  <CheckCircle className="h-6 w-6 text-green-500" />
                  <div>
                    <p className="font-semibold">24/7 Emergency</p>
                    <p className="text-sm text-gray-600">Always Available</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Stats Section */}
      <section className="py-16 bg-white">
        <div className="container mx-auto px-4">
          <div className="grid grid-cols-2 md:grid-cols-4 gap-8">
            {stats.map((stat, index) => {
              const IconComponent = stat.icon
              return (
                <div key={index} className="text-center">
                  <div className="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <IconComponent className="h-8 w-8 text-blue-600" />
                  </div>
                  <h3 className="text-3xl font-bold text-blue-900 mb-2">{stat.number}</h3>
                  <p className="text-gray-600">{stat.label}</p>
                </div>
              )
            })}
          </div>
        </div>
      </section>

      {/* Departments Section */}
      <section id="departments" className="py-20 bg-gray-50">
        <div className="container mx-auto px-4">
          <div className="text-center mb-16">
            <h2 className="text-4xl font-bold text-gray-900 mb-4">Our Departments</h2>
            <p className="text-xl text-gray-600 max-w-2xl mx-auto">
              Comprehensive healthcare services across multiple specializations with expert medical professionals
            </p>
          </div>
          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            {departments.map((dept, index) => {
              const IconComponent = dept.icon
              return (
                <Card key={index} className="hover:shadow-lg transition-shadow duration-300 cursor-pointer">
                  <CardContent className="p-6">
                    <div className={`w-16 h-16 rounded-lg ${dept.color} flex items-center justify-center mb-4`}>
                      <IconComponent className="h-8 w-8" />
                    </div>
                    <h3 className="text-xl font-semibold mb-2">{dept.name}</h3>
                    <p className="text-gray-600 mb-4">{dept.description}</p>
                    <Button variant="ghost" className="text-blue-600 hover:text-blue-700 p-0">
                      Learn More →
                    </Button>
                  </CardContent>
                </Card>
              )
            })}
          </div>
        </div>
      </section>

      {/* Doctors Section */}
      <section id="doctors" className="py-20 bg-white">
        <div className="container mx-auto px-4">
          <div className="text-center mb-16">
            <h2 className="text-4xl font-bold text-gray-900 mb-4">Meet Our Doctors</h2>
            <p className="text-xl text-gray-600 max-w-2xl mx-auto">
              Our team of experienced and dedicated healthcare professionals
            </p>
          </div>
          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            {doctors.map((doctor, index) => (
              <Card key={index} className="hover:shadow-lg transition-shadow duration-300">
                <CardContent className="p-6 text-center">
                  <Image
                    src={doctor.image || "/placeholder.svg"}
                    alt={doctor.name}
                    width={150}
                    height={150}
                    className="rounded-full mx-auto mb-4"
                  />
                  <h3 className="text-xl font-semibold mb-2">{doctor.name}</h3>
                  <Badge variant="secondary" className="mb-2">
                    {doctor.specialization}
                  </Badge>
                  <p className="text-gray-600 mb-2">{doctor.experience} experience</p>
                  <div className="flex items-center justify-center mb-4">
                    <Star className="h-4 w-4 text-yellow-400 fill-current" />
                    <span className="ml-1 text-sm font-medium">{doctor.rating}</span>
                  </div>
                  <Button className="w-full">Book Appointment</Button>
                </CardContent>
              </Card>
            ))}
          </div>
        </div>
      </section>

      {/* Quick Appointment Section */}
      <section className="py-20 bg-blue-600 text-white">
        <div className="container mx-auto px-4">
          <div className="max-w-4xl mx-auto text-center">
            <h2 className="text-4xl font-bold mb-6">Need Immediate Care?</h2>
            <p className="text-xl mb-8 text-blue-100">
              Don't wait. Book your appointment now or call our emergency hotline for immediate assistance.
            </p>
            <div className="grid md:grid-cols-3 gap-6">
              <Card className="bg-white text-gray-900">
                <CardContent className="p-6 text-center">
                  <Clock className="h-12 w-12 text-blue-600 mx-auto mb-4" />
                  <h3 className="text-lg font-semibold mb-2">Quick Booking</h3>
                  <p className="text-sm text-gray-600 mb-4">Book appointment in under 2 minutes</p>
                  <Button className="w-full bg-blue-600 hover:bg-blue-700">Book Now</Button>
                </CardContent>
              </Card>
              <Card className="bg-white text-gray-900">
                <CardContent className="p-6 text-center">
                  <Phone className="h-12 w-12 text-green-600 mx-auto mb-4" />
                  <h3 className="text-lg font-semibold mb-2">Emergency Call</h3>
                  <p className="text-sm text-gray-600 mb-4">24/7 emergency helpline</p>
                  <Button className="w-full bg-green-600 hover:bg-green-700">Call 911</Button>
                </CardContent>
              </Card>
              <Card className="bg-white text-gray-900">
                <CardContent className="p-6 text-center">
                  <Mail className="h-12 w-12 text-purple-600 mx-auto mb-4" />
                  <h3 className="text-lg font-semibold mb-2">Online Consultation</h3>
                  <p className="text-sm text-gray-600 mb-4">Video call with doctors</p>
                  <Button className="w-full bg-purple-600 hover:bg-purple-700">Start Chat</Button>
                </CardContent>
              </Card>
            </div>
          </div>
        </div>
      </section>

      {/* Contact Section */}
      <section id="contact" className="py-20 bg-gray-900 text-white">
        <div className="container mx-auto px-4">
          <div className="grid lg:grid-cols-2 gap-12">
            <div>
              <h2 className="text-4xl font-bold mb-6">Get in Touch</h2>
              <p className="text-xl text-gray-300 mb-8">
                We're here for you 24/7. Reach out to us for any medical emergency or general inquiries.
              </p>
              <div className="space-y-6">
                <div className="flex items-center space-x-4">
                  <MapPin className="h-6 w-6 text-blue-400" />
                  <div>
                    <p className="font-semibold">Address</p>
                    <p className="text-gray-300">123 Healthcare Ave, Medical District, NY 10001</p>
                  </div>
                </div>
                <div className="flex items-center space-x-4">
                  <Phone className="h-6 w-6 text-blue-400" />
                  <div>
                    <p className="font-semibold">Phone</p>
                    <p className="text-gray-300">+1 (555) 123-4567</p>
                  </div>
                </div>
                <div className="flex items-center space-x-4">
                  <Mail className="h-6 w-6 text-blue-400" />
                  <div>
                    <p className="font-semibold">Email</p>
                    <p className="text-gray-300">info@healthcareplus.com</p>
                  </div>
                </div>
              </div>
            </div>
            <div className="bg-gray-800 p-8 rounded-lg">
              <h3 className="text-2xl font-bold mb-6">Send us a Message</h3>
              <form className="space-y-4">
                <div className="grid md:grid-cols-2 gap-4">
                  <input
                    type="text"
                    placeholder="First Name"
                    className="bg-gray-700 text-white px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                  />
                  <input
                    type="text"
                    placeholder="Last Name"
                    className="bg-gray-700 text-white px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                  />
                </div>
                <input
                  type="email"
                  placeholder="Email Address"
                  className="w-full bg-gray-700 text-white px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
                <textarea
                  placeholder="Your Message"
                  rows={4}
                  className="w-full bg-gray-700 text-white px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                ></textarea>
                <Button className="w-full bg-blue-600 hover:bg-blue-700 py-3">Send Message</Button>
              </form>
            </div>
          </div>
        </div>
      </section>

      {/* Footer */}
      <footer className="bg-black text-white py-12">
        <div className="container mx-auto px-4">
          <div className="grid md:grid-cols-4 gap-8">
            <div>
              <div className="flex items-center space-x-2 mb-4">
                <div className="bg-blue-600 p-2 rounded-lg">
                  <Stethoscope className="h-6 w-6 text-white" />
                </div>
                <h3 className="text-xl font-bold">HealthCare Plus</h3>
              </div>
              <p className="text-gray-400">Providing quality healthcare services with compassion and excellence.</p>
            </div>
            <div>
              <h4 className="text-lg font-semibold mb-4">Quick Links</h4>
              <ul className="space-y-2 text-gray-400">
                <li>
                  <Link href="#" className="hover:text-white">
                    About Us
                  </Link>
                </li>
                <li>
                  <Link href="#" className="hover:text-white">
                    Services
                  </Link>
                </li>
                <li>
                  <Link href="#" className="hover:text-white">
                    Doctors
                  </Link>
                </li>
                <li>
                  <Link href="#" className="hover:text-white">
                    Contact
                  </Link>
                </li>
              </ul>
            </div>
            <div>
              <h4 className="text-lg font-semibold mb-4">Services</h4>
              <ul className="space-y-2 text-gray-400">
                <li>
                  <Link href="#" className="hover:text-white">
                    Emergency Care
                  </Link>
                </li>
                <li>
                  <Link href="#" className="hover:text-white">
                    Surgery
                  </Link>
                </li>
                <li>
                  <Link href="#" className="hover:text-white">
                    Laboratory
                  </Link>
                </li>
                <li>
                  <Link href="#" className="hover:text-white">
                    Pharmacy
                  </Link>
                </li>
              </ul>
            </div>
            <div>
              <h4 className="text-lg font-semibold mb-4">Emergency</h4>
              <p className="text-gray-400 mb-2">24/7 Emergency Hotline:</p>
              <p className="text-2xl font-bold text-red-400">911</p>
              <p className="text-gray-400 mt-2">For non-emergency inquiries:</p>
              <p className="text-lg text-blue-400">+1 (555) 123-4567</p>
            </div>
          </div>
          <div className="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
            <p>&copy; 2024 HealthCare Plus. All rights reserved.</p>
          </div>
        </div>
      </footer>
    </div>
  )
}
